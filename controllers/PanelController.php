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
        if (in_array($action->id, ['boleto-change-estado', 'pago-change-estado', 'boleto-delete', 'verificar-password', 'aprobar-boleto-anulado', 'api-boletos-rifa', 'api-rifas-pendientes', 'api-new-boletos', 'api-panel-data'])) {
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
        return $this->redirect(['/testimonios/index']);
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
                    // Cambiar todos los pagos a fallidos
                    \app\models\Pagos::updateAll(
                        ['estado' => \app\models\Pagos::ESTADO_FAILED],
                        ['id_boleto' => $id, 'is_deleted' => 0]
                    );
                    $boleto->setEstadoToAnulado();

                    // Enviar correo de anulación al jugador
                    if ($boleto->jugador && $boleto->jugador->correo) {
                        try {
                            $this->enviarCorreoBoletoAnulado($boleto);
                        } catch (\Exception $e) {
                            Yii::error('Error al enviar correo de anulación: ' . $e->getMessage(), 'panel');
                        }
                    }
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

                    // Registrar en SorteosGanadores
                    try {
                        $rifaId = $boleto->id_rifa;
                        $sorteo = \app\models\Sorteos::find()->where(['id_rifa' => $rifaId])->one();

                        if (!$sorteo) {
                            $sorteo = new \app\models\Sorteos();
                            $sorteo->id_rifa = $rifaId;
                            $sorteo->fecha_sorteo = date('Y-m-d H:i:s');
                            $sorteo->descripcion = 'Asignación manual de ganador';
                            if (!$sorteo->save()) {
                                Yii::error('No se pudo crear sorteo automático para boleto ganador ' . $id);
                            }
                        }

                        if ($sorteo && $sorteo->id) {
                            $existeGanador = \app\models\SorteosGanadores::find()
                                ->where(['id_sorteo' => $sorteo->id, 'id_boleto' => $boleto->id])
                                ->exists();

                            if (!$existeGanador) {
                                $ganador = new \app\models\SorteosGanadores();
                                $ganador->id_sorteo = $sorteo->id;
                                $ganador->id_boleto = $boleto->id;
                                if (!$ganador->save()) {
                                    Yii::error('No se pudo guardar SorteosGanadores para boleto ' . $id);
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Yii::error('Error registrando ganador: ' . $e->getMessage(), 'panel');
                    }

                    // Enviar correo de ganador al jugador
                    if ($boleto->jugador && $boleto->jugador->correo) {
                        try {
                            $this->enviarCorreoBoletoGanador($boleto);
                        } catch (\Exception $e) {
                            Yii::error('Error al enviar correo de ganador: ' . $e->getMessage(), 'panel');
                        }
                    }
                    break;

                case 'reembolsado':
                    // Cambiar todos los pagos a reembolsados
                    \app\models\Pagos::updateAll(
                        ['estado' => \app\models\Pagos::ESTADO_REFUNDED],
                        ['id_boleto' => $id, 'is_deleted' => 0]
                    );
                    $boleto->setEstadoToReembolsado();

                    // Enviar correo de reembolso al jugador
                    if ($boleto->jugador && $boleto->jugador->correo) {
                        try {
                            $this->enviarCorreoBoletoReembolsado($boleto);
                        } catch (\Exception $e) {
                            Yii::error('Error al enviar correo de reembolso: ' . $e->getMessage(), 'panel');
                        }
                    }
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

    /**
     * Envía correo al jugador cuando el boleto es ganador
     * @param \app\models\Boletos $boleto
     * @return bool
     */
    private function enviarCorreoBoletoGanador($boleto)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            Yii::warning("No se puede enviar correo: jugador sin email para boleto {$boleto->id}", 'panel');
            return false;
        }

        $rifa = $boleto->rifa;
        $boletoNumeros = \app\models\BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        $statusUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/status', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-ganador', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $numeros,
                'statusUrl' => $statusUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('¡FELICITACIONES! Eres Ganador - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de ganador enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            } else {
                Yii::warning("Fallo al enviar correo de ganador a {$jugador->correo}", 'panel');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo de ganador: " . $e->getMessage(), 'panel');
            throw $e;
        }
    }

    /**
     * Envía correo al jugador cuando el boleto es reembolsado
     * @param \app\models\Boletos $boleto
     * @return bool
     */
    private function enviarCorreoBoletoReembolsado($boleto)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            Yii::warning("No se puede enviar correo: jugador sin email para boleto {$boleto->id}", 'panel');
            return false;
        }

        $rifa = $boleto->rifa;
        $boletoNumeros = \app\models\BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        $statusUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/status', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-reembolsado', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $numeros,
                'statusUrl' => $statusUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('Reembolso Procesado - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de reembolso enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            } else {
                Yii::warning("Fallo al enviar correo de reembolso a {$jugador->correo}", 'panel');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo de reembolso: " . $e->getMessage(), 'panel');
            throw $e;
        }
    }

    /**
     * Envía correo al jugador cuando el boleto es anulado
     * @param \app\models\Boletos $boleto
     * @return bool
     */
    private function enviarCorreoBoletoAnulado($boleto)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            Yii::warning("No se puede enviar correo: jugador sin email para boleto {$boleto->id}", 'panel');
            return false;
        }

        $rifa = $boleto->rifa;
        $boletoNumeros = \app\models\BoletoNumeros::find()
            ->where(['id_boleto' => $boleto->id, 'is_deleted' => 0])
            ->orderBy(['numero' => SORT_ASC])
            ->all();

        $numeros = [];
        foreach ($boletoNumeros as $bn) {
            $numeros[] = $bn->numero;
        }

        $resubmitUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/resubir-comprobante', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-anulado', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $numeros,
                'resubmitUrl' => $resubmitUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('Problema con tu Pago - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de anulación enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            } else {
                Yii::warning("Fallo al enviar correo de anulación a {$jugador->correo}", 'panel');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo de anulación: " . $e->getMessage(), 'panel');
            throw $e;
        }
    }

    /**
     * Verifica la contraseña del usuario actual (AJAX)
     */
    public function actionVerificarPassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $rawBody = Yii::$app->request->getRawBody();
        $data = json_decode($rawBody, true);
        $password = $data['password'] ?? null;

        if (!$password) {
            return ['success' => false, 'message' => 'Contraseña requerida'];
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            return ['success' => false, 'message' => 'Usuario no autenticado'];
        }

        if ($user->validatePassword($password)) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
        }
    }

    /**
     * Aprueba manualmente un boleto anulado, reasignando números si es necesario (AJAX)
     */
    public function actionAprobarBoletoAnulado()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'message' => 'Método no permitido'];
        }

        $rawBody = Yii::$app->request->getRawBody();
        $data = json_decode($rawBody, true);

        $boletoId = $data['boletoId'] ?? null;
        $rifaId = $data['rifaId'] ?? null;

        if (!$boletoId || !$rifaId) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $boleto = \app\models\Boletos::findOne(['id' => $boletoId, 'is_deleted' => 0]);
        if (!$boleto) {
            return ['success' => false, 'message' => 'Boleto no encontrado'];
        }

        if ($boleto->estado !== \app\models\Boletos::ESTADO_ANULADO) {
            return ['success' => false, 'message' => 'Solo se pueden aprobar boletos anulados'];
        }

        $rifa = \app\models\Rifas::findOne(['id' => $rifaId, 'is_deleted' => 0]);
        if (!$rifa) {
            return ['success' => false, 'message' => 'Rifa no encontrada'];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Obtener números actuales del boleto
            $numerosActuales = \app\models\BoletoNumeros::find()
                ->where(['id_boleto' => $boletoId, 'is_deleted' => 0])
                ->all();

            $cantidadNumeros = count($numerosActuales);
            $numerosOriginales = [];
            foreach ($numerosActuales as $bn) {
                $numerosOriginales[] = $bn->numero;
            }

            // Verificar qué números ya fueron tomados por boletos pagados
            $numerosTomados = \app\models\BoletoNumeros::find()
                ->alias('bn')
                ->innerJoin('boletos b', 'b.id = bn.id_boleto')
                ->where([
                    'b.id_rifa' => $rifaId,
                    'b.estado' => \app\models\Boletos::ESTADO_PAGADO,
                    'b.is_deleted' => 0,
                    'bn.is_deleted' => 0
                ])
                ->andWhere(['bn.numero' => $numerosOriginales])
                ->select('bn.numero')
                ->column();

            $numerosReasignados = false;
            $numerosNuevos = [];

            if (!empty($numerosTomados)) {
                // Necesitamos reasignar números
                $numerosReasignados = true;

                // Obtener números disponibles
                $numerosDisponibles = $rifa->getNumerosDisponiblesArray();

                if (count($numerosDisponibles) < $cantidadNumeros) {
                    // No hay suficientes números, asignar solo los disponibles
                    $cantidadNumeros = count($numerosDisponibles);
                }

                if ($cantidadNumeros == 0) {
                    throw new \Exception('No hay números disponibles en la rifa');
                }

                // Seleccionar números aleatorios
                shuffle($numerosDisponibles);
                $numerosNuevos = array_slice($numerosDisponibles, 0, $cantidadNumeros);

                // Eliminar números antiguos (soft delete)
                \app\models\BoletoNumeros::updateAll(
                    ['is_deleted' => 1],
                    ['id_boleto' => $boletoId]
                );

                // Insertar nuevos números
                foreach ($numerosNuevos as $numero) {
                    $bn = new \app\models\BoletoNumeros();
                    $bn->id_boleto = $boletoId;
                    $bn->numero = $numero;
                    $bn->save();
                }
            } else {
                // Los números originales están disponibles
                $numerosNuevos = $numerosOriginales;
            }

            // Actualizar precio del boleto según la nueva cantidad
            $boleto->cantidad_numeros = $cantidadNumeros;
            $boleto->total_precio = $rifa->precio_boleto * $cantidadNumeros;

            // Cambiar estado a pagado
            $boleto->setEstadoToPagado();
            if (!$boleto->save()) {
                throw new \Exception('Error al actualizar el boleto');
            }

            // Actualizar pagos a confirmados
            \app\models\Pagos::updateAll(
                [
                    'estado' => \app\models\Pagos::ESTADO_CONFIRMED,
                    'monto' => $boleto->total_precio
                ],
                ['id_boleto' => $boletoId, 'is_deleted' => 0]
            );

            $transaction->commit();

            // Enviar email al jugador notificando la aprobación
            try {
                $this->enviarCorreoBoletoAprobado($boleto, $numerosNuevos, $numerosReasignados);
            } catch (\Exception $e) {
                Yii::warning('Error al enviar correo de aprobación: ' . $e->getMessage(), 'panel');
            }

            return [
                'success' => true,
                'numerosReasignados' => $numerosReasignados,
                'numerosNuevos' => $numerosNuevos,
                'cantidadAsignada' => $cantidadNumeros
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Envía correo al jugador cuando su boleto es aprobado manualmente
     * @param \app\models\Boletos $boleto
     * @param array $nuevosNumeros
     * @param bool $fueronReasignados
     * @return bool
     */
    private function enviarCorreoBoletoAprobado($boleto, $nuevosNumeros, $fueronReasignados)
    {
        $jugador = $boleto->jugador;

        if (!$jugador || !$jugador->correo) {
            return false;
        }

        $rifa = $boleto->rifa;
        $statusUrl = Yii::$app->urlManager->createAbsoluteUrl(['boletos/status', 'id' => $boleto->id]);

        try {
            $sent = Yii::$app->mailer->compose('boleto-confirmed', [
                'boleto' => $boleto,
                'rifa' => $rifa,
                'numeros' => $nuevosNumeros,
                'statusUrl' => $statusUrl,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                ->setTo($jugador->correo)
                ->setSubject('¡Tu pago ha sido confirmado! - ' . $rifa->titulo)
                ->send();

            if ($sent) {
                Yii::info("Correo de aprobación enviado a {$jugador->correo} para boleto {$boleto->codigo}", 'panel');
            }

            return $sent;
        } catch (\Exception $e) {
            Yii::error("Excepción al enviar correo de aprobación: " . $e->getMessage(), 'panel');
            throw $e;
        }
    }

    /**
     * API: Obtener boletos de una rifa para actualización en tiempo real
     */
    public function actionApiBoletosRifa($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $rifa = \app\models\Rifas::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$rifa) {
            return ['success' => false, 'message' => 'Rifa no encontrada'];
        }

        $boletos = \app\models\Boletos::find()
            ->where(['id_rifa' => $id, 'is_deleted' => 0])
            ->with(['jugador'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        // Solo contar pagado para progress (no ganador, porque ya es pagado)
        $numerosJugados = \app\models\BoletoNumeros::find()
            ->joinWith(['boleto'])
            ->where([
                'boletos.id_rifa' => $id,
                'boletos.estado' => \app\models\Boletos::ESTADO_PAGADO,
                'boletos.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->count();

        // También contar ganadores (porque ganador implica pagado)
        $numerosGanador = \app\models\BoletoNumeros::find()
            ->joinWith(['boleto'])
            ->where([
                'boletos.id_rifa' => $id,
                'boletos.estado' => \app\models\Boletos::ESTADO_GANADOR,
                'boletos.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->count();

        $totalNumerosJugados = $numerosJugados + $numerosGanador;
        $porcentaje = $rifa->max_numeros > 0 ? ($totalNumerosJugados / $rifa->max_numeros) * 100 : 0;

        // Generate HTML for boletos
        $boletosHtml = '';
        if (empty($boletos)) {
            $boletosHtml = '<div class="empty-state"><div class="empty-icon"><i class="fas fa-inbox"></i></div><p class="mb-0">No hay boletos registrados para esta rifa</p></div>';
        } else {
            foreach ($boletos as $boleto) {
                $jugadorNombre = $boleto->jugador ? htmlspecialchars($boleto->jugador->nombre) : 'N/A';
                $jugadorTelefono = $boleto->jugador && $boleto->jugador->telefono ? htmlspecialchars($boleto->jugador->telefono) : 'N/A';
                $estadoDisplay = htmlspecialchars($boleto->displayEstado());
                $viewUrl = Yii::$app->urlManager->createUrl(['/panel/boletos-view', 'id' => $boleto->id]);

                $boletosHtml .= <<<HTML
<div class="boleto-row">
    <div class="boleto-info-grid">
        <div class="boleto-field">
            <div class="boleto-label">Código</div>
            <div class="boleto-value">{$boleto->codigo}</div>
        </div>
        <div class="boleto-field">
            <div class="boleto-label">Jugador</div>
            <div class="boleto-value">{$jugadorNombre}</div>
        </div>
        <div class="boleto-field">
            <div class="boleto-label">Teléfono</div>
            <div class="boleto-value">{$jugadorTelefono}</div>
        </div>
        <div class="boleto-field">
            <div class="boleto-label">Estado</div>
            <div class="boleto-value">
                <span class="estado-badge estado-{$boleto->estado}">{$estadoDisplay}</span>
            </div>
        </div>
    </div>
    <a href="{$viewUrl}" class="btn-ver-detalle"><i class="fas fa-eye me-1"></i> Ver</a>
</div>
HTML;
            }
        }

        return [
            'success' => true,
            'boletosHtml' => $boletosHtml,
            'numerosJugados' => $totalNumerosJugados,
            'porcentaje' => $porcentaje,
            'totalBoletos' => count($boletos)
        ];
    }

    /**
     * API: Obtener rifas pendientes de ganadores (sorteo vencido pero sin sortear)
     */
    public function actionApiRifasPendientes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $ahora = date('Y-m-d H:i:s');

        $rifas = \app\models\Rifas::find()
            ->where(['estado' => \app\models\Rifas::ESTADO_ACTIVA, 'is_deleted' => 0])
            ->andWhere(['<=', 'fecha_sorteo', $ahora])
            ->all();

        $rifasData = [];
        foreach ($rifas as $rifa) {
            $rifasData[] = [
                'id' => $rifa->id,
                'titulo' => $rifa->titulo,
                'fecha_sorteo' => $rifa->fecha_sorteo,
                'viewUrl' => Yii::$app->urlManager->createUrl(['/panel/rifas-view', 'id' => $rifa->id])
            ];
        }

        return [
            'success' => true,
            'rifas' => $rifasData,
            'count' => count($rifasData)
        ];
    }

    /**
     * API: Obtener nuevos boletos para notificaciones toast (solo no leídos)
     */
    public function actionApiNewBoletos()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Obtener boletos no leídos
        $boletos = \app\models\Boletos::find()
            ->where(['is_readed' => 0, 'is_deleted' => 0])
            ->orderBy(['id' => SORT_ASC])
            ->limit(10)
            ->all();

        $boletosData = [];
        $idsToMark = [];

        foreach ($boletos as $boleto) {
            $boletosData[] = [
                'id' => $boleto->id,
                'codigo' => $boleto->codigo,
                'cantidad_numeros' => $boleto->cantidad_numeros,
                'viewUrl' => Yii::$app->urlManager->createUrl(['/panel/boletos-view', 'id' => $boleto->id])
            ];
            $idsToMark[] = $boleto->id;
        }

        // Marcar como leídos
        if (!empty($idsToMark)) {
            \app\models\Boletos::updateAll(
                ['is_readed' => 1],
                ['id' => $idsToMark]
            );
        }

        return [
            'success' => true,
            'boletos' => $boletosData
        ];
    }


    /**
     * API: Obtener datos del panel para actualización en tiempo real
     */
    public function actionApiPanelData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $estadoFiltro = Yii::$app->request->get('estado', \app\models\Rifas::ESTADO_ACTIVA);

        // Obtener rifas según el filtro
        $rifasFiltradas = \app\models\Rifas::find()
            ->where(['estado' => $estadoFiltro, 'is_deleted' => 0])
            ->all();

        // Datos de rifas
        $rifasData = [];
        foreach ($rifasFiltradas as $rifa) {
            $segundosRecaudacion = $rifa->getSegundosHastaFinRecaudacion();
            $segundosSorteo = $rifa->getSegundosHastaSorteo();

            $rifasData[] = [
                'id' => $rifa->id,
                'titulo' => $rifa->titulo,
                'estado' => $rifa->estado,
                'precio_boleto' => $rifa->precio_boleto,
                'fecha_fin' => $rifa->fecha_fin,
                'segundosRecaudacion' => $segundosRecaudacion,
                'segundosSorteo' => $segundosSorteo,
                'viewUrl' => Yii::$app->urlManager->createUrl(['/panel/rifas-view', 'id' => $rifa->id])
            ];
        }

        // Boletos reservados
        $boletosReservados = \app\models\Boletos::find()
            ->where(['estado' => \app\models\Boletos::ESTADO_RESERVADO, 'is_deleted' => 0])
            ->with('jugador')
            ->orderBy(['reserved_until' => SORT_ASC])
            ->all();

        $boletosData = [];
        foreach ($boletosReservados as $boleto) {
            $boletosData[] = [
                'id' => $boleto->id,
                'codigo' => $boleto->codigo,
                'jugador_nombre' => $boleto->jugador ? $boleto->jugador->nombre : 'N/A',
                'reserved_until' => $boleto->reserved_until,
                'id_rifa' => $boleto->id_rifa,
                'viewUrl' => Yii::$app->urlManager->createUrl(['/panel/boletos-view', 'id' => $boleto->id]),
                'rifaViewUrl' => Yii::$app->urlManager->createUrl(['/panel/rifas-view', 'id' => $boleto->id_rifa])
            ];
        }

        // Contadores
        $totalReservados = \app\models\Boletos::find()->where(['estado' => \app\models\Boletos::ESTADO_RESERVADO, 'is_deleted' => 0])->count();
        $totalPagados = \app\models\Boletos::find()->where(['estado' => \app\models\Boletos::ESTADO_PAGADO, 'is_deleted' => 0])->count();
        $totalAnulados = \app\models\Boletos::find()->where(['estado' => \app\models\Boletos::ESTADO_ANULADO, 'is_deleted' => 0])->count();
        $totalReembolsados = \app\models\Boletos::find()->where(['estado' => \app\models\Boletos::ESTADO_REEMBOLSADO, 'is_deleted' => 0])->count();

        return [
            'success' => true,
            'rifas' => $rifasData,
            'boletosReservados' => $boletosData,
            'totales' => [
                'reservados' => (int) $totalReservados,
                'pagados' => (int) $totalPagados,
                'anulados' => (int) $totalAnulados,
                'reembolsados' => (int) $totalReembolsados
            ]
        ];
    }
}
