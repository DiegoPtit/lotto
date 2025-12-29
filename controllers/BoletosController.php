<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;
use app\models\Rifas;
use app\models\Boletos;
use app\models\BoletoNumeros;
use app\models\Jugadores;
use app\models\Pagos;
use app\models\MetodosPago;
use app\models\AuditLogs;

/**
 * BoletosController maneja las operaciones AJAX para la compra de boletos
 */
class BoletosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'except' => ['status', 'resubir-comprobante'], // Excluir acción status para que devuelva HTML
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'obtener-metodos-pago' => ['get'],
                    'verificar-disponibilidad' => ['post'],
                    'procesar-boleto' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Deshabilitar CSRF para llamadas AJAX
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Obtiene los métodos de pago públicos con sus campos según el tipo
     * @return array
     */
    public function actionObtenerMetodosPago()
    {
        $metodos = MetodosPago::find()
            ->where(['visibilidad' => MetodosPago::VISIBILIDAD_PUBLICA])
            ->with(['tipo'])
            ->all();

        $result = [];
        foreach ($metodos as $metodo) {
            $tipo = $metodo->tipo;
            $fields = [];

            if ($tipo->has_banco && $metodo->banco) {
                $fields['banco'] = $metodo->banco;
            }
            if ($tipo->has_titular && $metodo->titular) {
                $fields['titular'] = $metodo->titular;
            }
            if ($tipo->has_cedula && $metodo->cedula) {
                $fields['cedula'] = $metodo->cedula;
            }
            if ($tipo->has_telefono && $metodo->telefono) {
                $fields['telefono'] = $metodo->telefono;
            }
            if ($tipo->has_nro_cuenta && $metodo->nro_cuenta) {
                $fields['nro_cuenta'] = $metodo->nro_cuenta;
            }

            $result[] = [
                'id' => $metodo->id,
                'tipo' => $tipo->descripcion,
                'fields' => $fields,
            ];
        }

        return ['success' => true, 'metodos' => $result];
    }

    /**
     * Verifica la disponibilidad de números para una rifa
     * @return array
     */
    public function actionVerificarDisponibilidad()
    {
        $request = Yii::$app->request;
        $idRifa = $request->post('id_rifa');
        $cantidad = (int) $request->post('cantidad', 1);

        if (!$idRifa) {
            return ['success' => false, 'message' => 'ID de rifa requerido'];
        }

        $rifa = Rifas::findOne(['id' => $idRifa, 'is_deleted' => 0]);
        if (!$rifa) {
            return ['success' => false, 'message' => 'Rifa no encontrada'];
        }

        if (!$rifa->isEstadoActiva()) {
            return ['success' => false, 'message' => 'Esta rifa no está activa'];
        }

        $disponibles = $rifa->getNumerosDisponibles();
        $cantidadFinal = min($cantidad, $disponibles);

        return [
            'success' => true,
            'disponibles' => $disponibles,
            'cantidad_solicitada' => $cantidad,
            'cantidad_asignable' => $cantidadFinal,
            'max_numeros' => $rifa->max_numeros,
            'precio_unitario' => (float) $rifa->precio_boleto,
            'moneda' => $rifa->moneda,
            'total' => $cantidadFinal * (float) $rifa->precio_boleto,
        ];
    }

    /**
     * Procesa la compra completa del boleto
     * @return array
     */
    public function actionProcesarBoleto()
    {
        $request = Yii::$app->request;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Leer el JSON del body
            $rawBody = $request->getRawBody();
            $data = json_decode($rawBody, true);

            if (!$data) {
                throw new \Exception('Datos JSON inválidos');
            }

            // Obtener datos del request
            $idRifa = $data['id_rifa'] ?? null;
            $cantidad = (int) ($data['cantidad'] ?? 0);
            $aceptaCondiciones = $data['acepta_condiciones'] ?? false;
            $jugadorData = $data['jugador'] ?? [];
            $pagoData = $data['pago'] ?? [];

            // Validaciones básicas
            if (!$idRifa || $cantidad < 1) {
                throw new \Exception('Datos de rifa inválidos');
            }

            if (!$aceptaCondiciones) {
                throw new \Exception('Debe aceptar los términos y condiciones');
            }

            // Validar campos del jugador
            $requiredFields = ['cedula', 'nombre', 'telefono'];
            foreach ($requiredFields as $field) {
                if (empty($jugadorData[$field])) {
                    throw new \Exception("El campo {$field} es obligatorio");
                }
            }

            // Validar datos de pago
            if (empty($pagoData['transaction_id'])) {
                throw new \Exception('La referencia de pago es obligatoria');
            }

            // Validar que se haya seleccionado un método de pago
            if (empty($pagoData['id_metodo_pago'])) {
                throw new \Exception('Debe seleccionar un método de pago');
            }

            // Obtener la rifa
            $rifa = Rifas::findOne(['id' => $idRifa, 'is_deleted' => 0]);
            if (!$rifa || !$rifa->isEstadoActiva()) {
                throw new \Exception('Rifa no disponible');
            }

            // Verificar disponibilidad
            $disponibles = $rifa->getNumerosDisponibles();
            if ($disponibles < $cantidad) {
                $cantidad = $disponibles;
                if ($cantidad < 1) {
                    throw new \Exception('No hay números disponibles');
                }
            }

            // 1. Crear o buscar jugador
            $jugador = Jugadores::findOne(['cedula' => $jugadorData['cedula'], 'is_deleted' => 0]);
            if (!$jugador) {
                $jugador = new Jugadores();
                $jugador->cedula = $jugadorData['cedula'];
                $jugador->nombre = $jugadorData['nombre'];
                $jugador->pais = $jugadorData['pais'] ?? null;
                $jugador->telefono = $jugadorData['telefono'];
                $jugador->correo = $jugadorData['correo'] ?? null;

                if (!$jugador->save()) {
                    throw new \Exception('Error al crear jugador: ' . implode(', ', $jugador->getFirstErrors()));
                }

                $this->logAudit('jugador_created', 'jugadores', $jugador->id, $jugadorData);
            } else {
                // Actualizar datos del jugador existente
                $jugador->nombre = $jugadorData['nombre'];
                $jugador->pais = $jugadorData['pais'] ?? $jugador->pais;
                $jugador->telefono = $jugadorData['telefono'];
                // No actualizar correo si ya existe para evitar conflictos de unicidad
                $jugador->save();
            }

            // 2. Generar código único de boleto
            $codigoBoleto = $this->generarCodigoBoleto($rifa->id);
            $reservaToken = Yii::$app->security->generateRandomString(32);

            // 3. Crear boleto
            $boleto = new Boletos();
            $boleto->codigo = $codigoBoleto;
            $boleto->id_rifa = $rifa->id;
            $boleto->id_jugador = $jugador->id;
            $boleto->cantidad_numeros = $cantidad;
            $boleto->total_precio = $cantidad * $rifa->precio_boleto;
            $boleto->estado = Boletos::ESTADO_RESERVADO;
            $boleto->acepta_condiciones = 1;
            $boleto->reserved_until = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $boleto->reserva_token = $reservaToken;

            if (!$boleto->save()) {
                throw new \Exception('Error al crear boleto: ' . implode(', ', $boleto->getFirstErrors()));
            }

            $this->logAudit('boleto_created', 'boletos', $boleto->id, [
                'codigo' => $codigoBoleto,
                'cantidad' => $cantidad,
                'total' => $boleto->total_precio,
            ]);

            // 4. Generar números únicos (ruta segura)
            $numerosGenerados = $this->generarNumerosUnicos($rifa, $cantidad);
            foreach ($numerosGenerados as $numero) {
                $boletoNumero = new BoletoNumeros();
                $boletoNumero->id_boleto = $boleto->id;
                $boletoNumero->numero = $numero;
                $boletoNumero->is_golden = 0;

                if (!$boletoNumero->save()) {
                    throw new \Exception('Error al crear número de boleto');
                }
            }

            $this->logAudit('numeros_assigned', 'boleto_numeros', $boleto->id, [
                'numeros' => $numerosGenerados,
            ]);

            // 5. Subir comprobante de pago (si existe)
            $comprobanteUrl = null;
            if (!empty($pagoData['comprobante'])) {
                $comprobanteUrl = $this->subirComprobante($pagoData['comprobante'], $boleto->id);
            }

            // 6. Crear registro de pago
            $pago = new Pagos();
            $pago->id_boleto = $boleto->id;
            $pago->id_jugador = $jugador->id;
            $pago->monto = $boleto->total_precio;
            $pago->moneda = 'VES';
            $pago->transaction_id = $pagoData['transaction_id'];
            $pago->id_metodo_pago = (int) $pagoData['id_metodo_pago'];
            $pago->estado = Pagos::ESTADO_PENDING;
            $pago->comprobante_url = $comprobanteUrl;
            $pago->notas = sprintf(
                'JUGADOR: %s REF: %s URL: %s MONTO: %.2f %s DIAHORA: %s',
                $jugador->nombre,
                $pagoData['transaction_id'],
                $comprobanteUrl ?? 'N/A',
                $boleto->total_precio,
                'VES',
                date('Y-m-d H:i:s')
            );

            if (!$pago->save()) {
                throw new \Exception('Error al crear pago: ' . implode(', ', $pago->getFirstErrors()));
            }

            $this->logAudit('pago_created', 'pagos', $pago->id, [
                'monto' => $pago->monto,
                'transaction_id' => $pago->transaction_id,
            ]);

            // 7. Enviar correo de notificación al jugador
            if ($jugador->correo) {
                try {
                    $this->enviarCorreoBoletoEnProceso($boleto, $rifa, $numerosGenerados);
                } catch (\Exception $e) {
                    // Error al enviar correo, pero no detenemos el proceso
                    Yii::error('Error al enviar correo de notificación: ' . $e->getMessage(), 'boletos');
                }
            }

            $transaction->commit();

            return [
                'success' => true,
                'mensaje' => '¡Boleto reservado exitosamente!',
                'boleto' => [
                    'id' => $boleto->id,
                    'codigo' => $boleto->codigo,
                    'numeros' => $numerosGenerados,
                    'total' => $boleto->total_precio,
                    'moneda' => $rifa->moneda,
                ],
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();

            $this->logAudit('boleto_error', 'boletos', null, [
                'error' => $e->getMessage(),
                'request' => $data ?? [],
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Genera un código único de boleto secuencial para la rifa
     * @param int $idRifa
     * @return string
     */
    private function generarCodigoBoleto($idRifa)
    {
        $ultimoBoleto = Boletos::find()
            ->where(['id_rifa' => $idRifa])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($ultimoBoleto) {
            $ultimoNumero = (int) $ultimoBoleto->codigo;
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Genera números únicos aleatorios para el boleto (ruta lenta pero segura)
     * @param Rifas $rifa
     * @param int $cantidad
     * @return array
     */
    private function generarNumerosUnicos($rifa, $cantidad)
    {
        // Obtener todos los números ya usados en esta rifa
        // Solo considerar números de boletos activos (no reembolsados ni anulados)
        $numerosUsados = BoletoNumeros::find()
            ->alias('bn')
            ->innerJoin(['b' => 'boletos'], 'bn.id_boleto = b.id')
            ->where(['b.id_rifa' => $rifa->id, 'b.is_deleted' => 0, 'bn.is_deleted' => 0])
            ->andWhere([
                'IN',
                'b.estado',
                [
                    Boletos::ESTADO_PAGADO,
                    Boletos::ESTADO_RESERVADO,
                    Boletos::ESTADO_GANADOR,
                ]
            ])
            ->select(['bn.numero'])
            ->column();

        $numerosUsadosSet = array_flip($numerosUsados);
        $maxNumeros = $rifa->max_numeros;
        $numerosGenerados = [];
        $intentos = 0;
        $maxIntentos = $maxNumeros * 10; // Límite de seguridad

        // Calcular cantidad de dígitos necesarios basándose en max_numeros
        $cantidadDigitos = strlen((string) $maxNumeros);

        while (count($numerosGenerados) < $cantidad && $intentos < $maxIntentos) {
            $intentos++;

            // Generar número aleatorio entre 1 y max_numeros
            $numero = rand(1, $maxNumeros);
            $numeroFormateado = str_pad($numero, $cantidadDigitos, '0', STR_PAD_LEFT);

            // Verificar si ya está usado o ya lo generamos
            if (!isset($numerosUsadosSet[$numeroFormateado]) && !in_array($numeroFormateado, $numerosGenerados)) {
                $numerosGenerados[] = $numeroFormateado;
            }
        }

        return $numerosGenerados;
    }

    /**
     * Sube el comprobante de pago al directorio local
     * @param string $base64Image Imagen en formato Base64
     * @param int $boletoId ID del boleto
     * @return string|null URL relativa del archivo guardado
     */
    private function subirComprobante($base64Image, $boletoId)
    {
        try {
            // Verificar que sea una imagen Base64 válida
            if (strpos($base64Image, 'data:image/') !== 0) {
                Yii::warning("Comprobante para boleto {$boletoId} no es una imagen Base64 válida", 'boletos');
                return null;
            }

            // Extraer el tipo de imagen y los datos
            preg_match('/data:image\/(\w+);base64,/', $base64Image, $matches);
            $extension = $matches[1] ?? 'png';

            // Validar extensión permitida
            $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array(strtolower($extension), $extensionesPermitidas)) {
                Yii::warning("Extensión de imagen no permitida: {$extension}", 'boletos');
                return null;
            }

            // Decodificar Base64
            $imageData = preg_replace('/data:image\/\w+;base64,/', '', $base64Image);
            $imageData = base64_decode($imageData);

            if ($imageData === false) {
                Yii::error("Error al decodificar Base64 para boleto {$boletoId}", 'boletos');
                return null;
            }

            // Crear directorio si no existe
            $uploadPath = Yii::getAlias('@webroot/uploads/comprobantes/');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único de archivo
            $fileName = 'pago_' . $boletoId . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath = $uploadPath . $fileName;

            // Guardar archivo
            if (file_put_contents($filePath, $imageData) === false) {
                Yii::error("Error al guardar comprobante en {$filePath}", 'boletos');
                return null;
            }

            Yii::info("Comprobante guardado exitosamente: {$fileName}", 'boletos');

            // Retornar URL relativa
            return '/uploads/comprobantes/' . $fileName;

        } catch (\Exception $e) {
            Yii::error("Error al subir comprobante: " . $e->getMessage(), 'boletos');
            return null;
        }
    }

    /**
     * Registra una acción en los logs de auditoría
     * @param string $accion
     * @param string $entidad
     * @param int|null $entidadId
     * @param array $datos
     */
    private function logAudit($accion, $entidad, $entidadId, $datos = [])
    {
        try {
            $audit = new AuditLogs();
            $audit->actor_type = AuditLogs::ACTOR_TYPE_JUGADOR;
            $audit->actor_id = null;
            $audit->accion = $accion;
            $audit->entidad = $entidad;
            $audit->entidad_id = $entidadId;
            $audit->datos = json_encode($datos);
            $audit->ip_address = Yii::$app->request->getUserIP();
            $audit->user_agent = Yii::$app->request->getUserAgent();
            $audit->save();
        } catch (\Exception $e) {
            Yii::error("Error en audit log: " . $e->getMessage(), 'audit');
        }
    }

    /**
     * Muestra el estado de un boleto específico
     * @param int $id ID del boleto
     * @return string
     */
    public function actionStatus($id)
    {
        $boleto = Boletos::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$boleto) {
            throw new \yii\web\NotFoundHttpException('El boleto solicitado no existe.');
        }

        // Cargar relaciones necesarias
        $boleto->refresh();
        $rifa = $boleto->rifa;
        $jugador = $boleto->jugador;

        // Obtener números jugados
        $boletoNumeros = BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        return $this->render('status', [
            'boleto' => $boleto,
            'rifa' => $rifa,
            'numeros' => $numeros,
        ]);
    }

    /**
     * Envía correo de notificación al jugador cuando el boleto está en proceso
     * @param Boletos $boleto
     * @param Rifas $rifa
     * @param array $numeros
     * @return bool
     */
    private function enviarCorreoBoletoEnProceso($boleto, $rifa, $numeros)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            Yii::warning("No se puede enviar correo: jugador sin email para boleto {$boleto->id}", 'boletos');
            return false;
        }

        // Generar URL absoluta para ver el estado del boleto
        $statusUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/status', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-processing', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $numeros,
                'statusUrl' => $statusUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('Tu boleto está en proceso - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de procesamiento enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'boletos');

                $this->logAudit('email_sent', 'boletos', $boleto->id, [
                    'email' => $jugador->correo,
                    'tipo' => 'boleto_processing',
                ]);
            } else {
                Yii::warning("Fallo al enviar correo a {$jugador->correo} para boleto {$boleto->codigo}", 'boletos');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo: " . $e->getMessage(), 'boletos');
            throw $e;
        }
    }

    /**
     * Permite a un usuario resubir un comprobante de pago para un boleto anulado
     * @param int $id ID del boleto
     * @return string|Response
     */
    public function actionResubirComprobante($id)
    {
        $boleto = Boletos::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$boleto) {
            throw new \yii\web\NotFoundHttpException('El boleto solicitado no existe.');
        }

        // Solo permitir si el boleto está anulado
        if ($boleto->estado !== Boletos::ESTADO_ANULADO) {
            Yii::$app->session->setFlash('error', 'Solo se puede resubir comprobante para boletos anulados.');
            return $this->redirect(['status', 'id' => $id]);
        }

        // Si es POST, procesar el nuevo comprobante
        if (Yii::$app->request->isPost) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Procesar archivo subido
                $file = \yii\web\UploadedFile::getInstanceByName('comprobante');

                if (!$file) {
                    return ['success' => false, 'message' => 'No se recibió ningún archivo'];
                }

                // Validar tipo de archivo
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                if (!in_array($file->type, $allowedTypes)) {
                    return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
                }

                // Validar tamaño (5MB max)
                if ($file->size > 5 * 1024 * 1024) {
                    return ['success' => false, 'message' => 'El archivo es muy grande (máx. 5MB)'];
                }

                // Convertir a Base64 si es imagen
                $base64 = null;
                if (str_starts_with($file->type, 'image/')) {
                    $imageData = file_get_contents($file->tempName);
                    $base64 = 'data:' . $file->type . ';base64,' . base64_encode($imageData);
                }

                // Encontrar o crear pago
                $pago = Pagos::findOne(['id_boleto' => $boleto->id, 'is_deleted' => 0]);

                if (!$pago) {
                    // Crear nuevo pago
                    $pago = new Pagos();
                    $pago->id_boleto = $boleto->id;
                    $pago->id_jugador = $boleto->id_jugador;
                    $pago->monto = $boleto->total_precio;
                    $pago->moneda = 'Bs.';
                    $pago->estado = Pagos::ESTADO_PENDING;
                    $pago->transaction_id = 'RESUBMIT-' . time() . '-' . $boleto->id;
                }

                // Subir comprobante
                if ($base64) {
                    $comprobanteUrl = $this->subirComprobante($base64, $boleto->id);
                    if ($comprobanteUrl) {
                        $pago->comprobante_url = $comprobanteUrl;
                    }
                }

                $pago->estado = Pagos::ESTADO_PENDING;
                $pago->setEstadoToPending();

                if (!$pago->save()) {
                    throw new \Exception('Error al actualizar el pago');
                }

                // Cambiar boleto a reservado con nueva fecha
                $boleto->estado = Boletos::ESTADO_RESERVADO;
                $boleto->setEstadoToReservado();
                $boleto->reserved_until = date('Y-m-d H:i:s', strtotime('+48 hours'));

                if (!$boleto->save()) {
                    throw new \Exception('Error al actualizar el boleto');
                }

                $transaction->commit();

                return [
                    'success' => true,
                    'message' => 'Comprobante recibido. Tu boleto está siendo procesado nuevamente.',
                    'redirect' => Yii::$app->urlManager->createUrl(['boletos/status', 'id' => $boleto->id])
                ];

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::error('Error en resubir comprobante: ' . $e->getMessage());
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }

        // GET: Mostrar formulario
        $rifa = $boleto->rifa;
        $jugador = $boleto->jugador;
        $boletoNumeros = BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        return $this->render('resubir-comprobante', [
            'boleto' => $boleto,
            'rifa' => $rifa,
            'jugador' => $jugador,
            'numeros' => $numeros,
        ]);
    }
}
