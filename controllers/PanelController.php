<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class PanelController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // Solo usuarios autenticados
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    // Si no está autenticado, redirigir a site/index
                    return Yii::$app->response->redirect(['/site/index']);
                },
            ],
        ];
    }

    /**
     * Deshabilitar CSRF para acciones AJAX
     */
    public function beforeAction($action)
    {
        // Deshabilitar CSRF para las acciones AJAX que reciben JSON
        if (in_array($action->id, ['boleto-change-estado', 'pago-change-estado', 'boleto-delete'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Displays panel homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'admin-main';

        // Obtener estado filtrado (por defecto: activa)
        $estadoFiltro = Yii::$app->request->get('estado', \app\models\Rifas::ESTADO_ACTIVA);

        // Validar que el estado sea válido
        $estadosValidos = [
            \app\models\Rifas::ESTADO_ACTIVA,
            \app\models\Rifas::ESTADO_BORRADOR,
            \app\models\Rifas::ESTADO_SORTEADA,
            \app\models\Rifas::ESTADO_CANCELADA,
        ];

        if (!in_array($estadoFiltro, $estadosValidos)) {
            $estadoFiltro = \app\models\Rifas::ESTADO_ACTIVA;
        }

        // Obtener rifas según el filtro
        $rifasFiltradas = \app\models\Rifas::find()
            ->where(['estado' => $estadoFiltro, 'is_deleted' => 0])
            ->all();

        // Obtener boletos reservados (pendientes de pago)
        $boletosReservados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_RESERVADO, 'is_deleted' => 0])
            ->with('jugador')
            ->orderBy(['reserved_until' => SORT_ASC])
            ->all();

        // Contar boletos por estado para el gráfico
        $totalReservados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_RESERVADO, 'is_deleted' => 0])
            ->count();

        $totalPagados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_PAGADO, 'is_deleted' => 0])
            ->count();

        $totalAnulados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_ANULADO, 'is_deleted' => 0])
            ->count();

        $totalReembolsados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_REEMBOLSADO, 'is_deleted' => 0])
            ->count();

        return $this->render('index', [
            'rifasFiltradas' => $rifasFiltradas,
            'estadoFiltro' => $estadoFiltro,
            'boletosReservados' => $boletosReservados,
            'totalReservados' => $totalReservados,
            'totalPagados' => $totalPagados,
            'totalAnulados' => $totalAnulados,
            'totalReembolsados' => $totalReembolsados,
        ]);
    }

    /**
     * Administrar Rifas
     */
    public function actionRifas()
    {
        $this->layout = 'admin-main';

        return $this->render('rifas/rifas');
    }

    /**
     * Administrar Testimonios
     */
    public function actionTestimonios()
    {
        $this->layout = 'admin-main';

        return $this->render('testimonios');
    }

    /**
     * Administrar Usuarios
     */
    public function actionUsuarios()
    {
        $this->layout = 'admin-main';

        return $this->render('usuarios');
    }

    /**
     * Administrar Métodos de Pago
     */
    public function actionMetodosPago()
    {
        $this->layout = 'admin-main';

        return $this->render('metodos-pago');
    }

    /**
     * Administrar Políticas
     */
    public function actionPoliticas()
    {
        $this->layout = 'admin-main';

        return $this->render('politicas');
    }

    /**
     * Histórico de Ganadores
     */
    public function actionGanadores()
    {
        $this->layout = 'admin-main';

        return $this->render('ganadores');
    }

    /**
     * Ver detalles de una rifa específica
     */
    public function actionRifasView($id)
    {
        $this->layout = 'admin-main';

        $rifa = \app\models\Rifas::findOne($id);

        if (!$rifa) {
            throw new \yii\web\NotFoundHttpException('La rifa solicitada no existe.');
        }

        // Obtener todos los boletos de esta rifa
        $boletos = \app\models\Boletos::find()
            ->where(['id_rifa' => $id, 'is_deleted' => 0])
            ->with(['jugador', 'boletoNumeros'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // Calcular el porcentaje de números jugados (solo boletos pagados)
        $numerosJugados = \app\models\BoletoNumeros::find()
            ->joinWith(['boleto'])
            ->where([
                'boletos.id_rifa' => $id,
                'boletos.estado' => \app\models\Boletos::ESTADO_PAGADO,
                'boletos.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->count();

        $porcentajeProgreso = $rifa->max_numeros > 0 ? ($numerosJugados / $rifa->max_numeros) * 100 : 0;

        // Top 3 jugadores con más números jugados (solo boletos pagados)
        $topJugadores = \app\models\Jugadores::find()
            ->select([
                'jugadores.*',
                'COUNT(boleto_numeros.id) as total_numeros'
            ])
            ->joinWith([
                'boletos' => function ($query) use ($id) {
                    $query->where([
                        'boletos.id_rifa' => $id,
                        'boletos.estado' => \app\models\Boletos::ESTADO_PAGADO,
                        'boletos.is_deleted' => 0
                    ]);
                }
            ])
            ->joinWith(['boletos.boletoNumeros'])
            ->where(['boleto_numeros.is_deleted' => 0])
            ->groupBy('jugadores.id')
            ->orderBy(['total_numeros' => SORT_DESC])
            ->limit(3)
            ->all();

        return $this->render('/rifas/view', [
            'rifa' => $rifa,
            'boletos' => $boletos,
            'numerosJugados' => $numerosJugados,
            'porcentajeProgreso' => $porcentajeProgreso,
            'topJugadores' => $topJugadores,
        ]);
    }

    /**
     * Ver detalles de un boleto específico
     */
    public function actionBoletosView($id)
    {
        $this->layout = 'admin-main';

        $boleto = \app\models\Boletos::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$boleto) {
            throw new \yii\web\NotFoundHttpException('El boleto solicitado no existe.');
        }

        // Cargar relaciones
        $jugador = $boleto->jugador;
        $rifa = $boleto->rifa;
        $boletoNumeros = \app\models\BoletoNumeros::find()
            ->where(['id_boleto' => $id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();
        $pagos = \app\models\Pagos::find()
            ->where(['id_boleto' => $id, 'is_deleted' => 0])
            ->with(['metodoPago.tipo', 'jugador'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('/boletos/view', [
            'boleto' => $boleto,
            'jugador' => $jugador,
            'rifa' => $rifa,
            'boletoNumeros' => $boletoNumeros,
            'pagos' => $pagos,
        ]);
    }

    /**
     * Cambiar estado de un boleto (AJAX)
     */
    public function actionBoletoChangeEstado()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);

        $id = $data['id'] ?? null;
        $nuevoEstado = $data['estado'] ?? null;

        if (!$id || !$nuevoEstado) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $boleto = \app\models\Boletos::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$boleto) {
            return ['success' => false, 'message' => 'Boleto no encontrado'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            switch ($nuevoEstado) {
                case 'anulado':
                    $boleto->setEstadoToAnulado();
                    break;

                case 'pagado':
                    // Cambiar todos los pagos a confirmados
                    \app\models\Pagos::updateAll(
                        ['estado' => \app\models\Pagos::ESTADO_CONFIRMED],
                        ['id_boleto' => $id, 'is_deleted' => 0]
                    );
                    $boleto->setEstadoToPagado();

                    // Enviar correo de confirmación al jugador
                    if ($boleto->jugador && $boleto->jugador->correo) {
                        try {
                            $this->enviarCorreoBoletoConfirmado($boleto);
                        } catch (\Exception $e) {
                            // Log error but don't fail the transaction
                            Yii::error('Error al enviar correo de confirmación: ' . $e->getMessage(), 'panel');
                        }
                    }
                    break;

                case 'ganador':
                    // Mantener pagos confirmados
                    $boleto->setEstadoToGanador();
                    break;

                case 'reembolsado':
                    // Cambiar todos los pagos a reembolsados
                    \app\models\Pagos::updateAll(
                        ['estado' => \app\models\Pagos::ESTADO_REFUNDED],
                        ['id_boleto' => $id, 'is_deleted' => 0]
                    );
                    $boleto->setEstadoToReembolsado();
                    break;

                default:
                    throw new \Exception('Estado no válido');
            }

            if (!$boleto->save()) {
                throw new \Exception('Error al actualizar el boleto');
            }

            $transaction->commit();
            return ['success' => true, 'message' => 'Estado actualizado correctamente'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cambiar estado de un pago (AJAX)
     */
    public function actionPagoChangeEstado()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);

        $id = $data['id'] ?? null;
        $nuevoEstado = $data['estado'] ?? null;

        if (!$id || !$nuevoEstado) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $pago = \app\models\Pagos::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$pago) {
            return ['success' => false, 'message' => 'Pago no encontrado'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $boleto = $pago->boleto;

            switch ($nuevoEstado) {
                case 'confirmed':
                    $pago->setEstadoToConfirmed();
                    $pago->save();

                    // Verificar si todos los pagos del boleto están confirmados
                    $pagosPendientes = \app\models\Pagos::find()
                        ->where([
                            'id_boleto' => $boleto->id,
                            'is_deleted' => 0
                        ])
                        ->andWhere(['!=', 'estado', \app\models\Pagos::ESTADO_CONFIRMED])
                        ->count();

                    if ($pagosPendientes === 0) {
                        $boleto->setEstadoToPagado();
                        $boleto->save();
                    }
                    break;

                case 'failed':
                    $pago->setEstadoToFailed();
                    $pago->save();

                    // Cambiar boleto a pendiente (reservado)
                    $boleto->setEstadoToReservado();
                    $boleto->save();
                    break;

                case 'refunded':
                    $pago->setEstadoToRefunded();
                    $pago->save();

                    // Cambiar boleto a reembolsado
                    $boleto->setEstadoToReembolsado();
                    $boleto->save();
                    break;

                default:
                    throw new \Exception('Estado no válido');
            }

            $transaction->commit();
            return ['success' => true, 'message' => 'Estado del pago actualizado correctamente'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Eliminar un boleto (soft delete) (AJAX)
     */
    public function actionBoletoDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if (!$request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $rawBody = $request->getRawBody();
        $data = json_decode($rawBody, true);

        $id = $data['id'] ?? null;

        if (!$id) {
            return ['success' => false, 'message' => 'ID de boleto requerido'];
        }

        $boleto = \app\models\Boletos::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$boleto) {
            return ['success' => false, 'message' => 'Boleto no encontrado'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Soft delete del boleto
            $boleto->is_deleted = 1;
            $boleto->deleted_at = date('Y-m-d H:i:s');
            $boleto->save();

            // Actualizar pagos relacionados
            \app\models\Pagos::updateAll(
                [
                    'estado' => \app\models\Pagos::ESTADO_FAILED,
                    'is_deleted' => 1
                ],
                ['id_boleto' => $id]
            );

            // Soft delete de números del boleto
            \app\models\BoletoNumeros::updateAll(
                ['is_deleted' => 1],
                ['id_boleto' => $id]
            );

            $transaction->commit();
            return ['success' => true, 'message' => 'Boleto eliminado correctamente'];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualizar un boleto (placeholder)
     */
    public function actionBoletosUpdate($id)
    {
        $this->layout = 'admin-main';

        return $this->render('/boletos/update', [
            'id' => $id
        ]);
    }

    /**
     * Envía correo de confirmación al jugador cuando el pago es validado
     * @param \app\models\Boletos $boleto
     * @return bool
     */
    private function enviarCorreoBoletoConfirmado($boleto)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            Yii::warning("No se puede enviar correo: jugador sin email para boleto {$boleto->id}", 'panel');
            return false;
        }

        // Cargar rifa
        $rifa = $boleto->rifa;

        // Obtener números jugados
        $boletoNumeros = \app\models\BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        // Generar URL absoluta para ver el estado del boleto
        $statusUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/status', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-confirmed', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $numeros,
                'statusUrl' => $statusUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('¡Tu pago ha sido confirmado! - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de confirmación enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            } else {
                Yii::warning("Fallo al enviar correo de confirmación a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo de confirmación: " . $e->getMessage(), 'panel');
            throw $e;
        }
    }
}
