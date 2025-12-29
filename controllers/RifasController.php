<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Rifas;
use app\models\Sorteos;
use app\models\Premios;
use app\models\BoletoNumeros;
use app\models\Boletos;

/**
 * RifasController maneja las vistas públicas de rifas
 */
class RifasController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete', 'activar', 'buscar-numero', 'set-ganador'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'activar', 'buscar-numero', 'set-ganador'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lista todas las rifas con paginación
     * @return string
     */
    public function actionIndex()
    {
        // Query para TODAS las rifas ordenadas por ventas
        $query = Rifas::find()
            ->select(['rifas.*', 'COUNT(boletos.id) as boletos_count'])
            ->leftJoin('boletos', 'boletos.id_rifa = rifas.id AND boletos.estado = :estado_pagado AND boletos.is_deleted = 0', [
                ':estado_pagado' => Boletos::ESTADO_PAGADO
            ])
            ->andWhere(['rifas.is_deleted' => 0])
            ->groupBy('rifas.id')
            ->orderBy(['boletos_count' => SORT_DESC]);

        // Paginación de 9 rifas por página
        $pagination = new Pagination([
            'totalCount' => (clone $query)->count(),
            'pageSize' => 9,
            'pageSizeParam' => false,
        ]);

        $rifas = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        // Calcular ranking global de ventas para las 3 mejores rifas activas
        $ventasPorRifa = [];
        $todasLasRifas = Rifas::find()
            ->select(['rifas.id', 'COUNT(boleto_numeros.id) as total_vendidos'])
            ->leftJoin('boletos', 'boletos.id_rifa = rifas.id AND boletos.is_deleted = 0')
            ->leftJoin('boleto_numeros', 'boleto_numeros.id_boleto = boletos.id AND boleto_numeros.is_deleted = 0')
            ->where(['rifas.estado' => Rifas::ESTADO_ACTIVA])
            ->andWhere(['rifas.is_deleted' => 0])
            ->andWhere(['IN', 'boletos.estado', [Boletos::ESTADO_PAGADO]])
            ->groupBy('rifas.id')
            ->orderBy(['total_vendidos' => SORT_DESC])
            ->asArray()
            ->all();

        foreach ($todasLasRifas as $r) {
            $ventasPorRifa[$r['id']] = (int) $r['total_vendidos'];
        }
        arsort($ventasPorRifa);
        $rankingIds = array_keys($ventasPorRifa);

        $this->layout = 'admin-main';

        return $this->render('index', [
            'rifas' => $rifas,
            'pagination' => $pagination,
            'rankingIds' => $rankingIds,
        ]);
    }

    /**
     * Muestra los detalles de una rifa específica
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $rifa = Rifas::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$rifa) {
            throw new \yii\web\NotFoundHttpException('La rifa no existe.');
        }

        // Obtener boletos de la rifa
        $boletos = Boletos::find()
            ->where(['id_rifa' => $id, 'is_deleted' => 0])
            ->with(['jugador', 'boletoNumeros'])
            ->all();

        // Números jugados (pagados)
        $numerosJugados = BoletoNumeros::find()
            ->joinWith('boleto')
            ->where([
                'boletos.id_rifa' => $id,
                'boletos.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->andWhere(['boletos.estado' => Boletos::ESTADO_PAGADO])
            ->count();

        $porcentajeProgreso = $rifa->max_numeros > 0
            ? round(($numerosJugados / $rifa->max_numeros) * 100, 2)
            : 0;

        // Top 3 jugadores por cantidad de números
        $topJugadores = (new \yii\db\Query())
            ->select([
                'jugadores.id',
                'jugadores.nombre',
                'jugadores.telefono',
                'COUNT(boleto_numeros.id) as total_numeros'
            ])
            ->from('jugadores')
            ->innerJoin('boletos', 'boletos.id_jugador = jugadores.id')
            ->innerJoin('boleto_numeros', 'boleto_numeros.id_boleto = boletos.id')
            ->where([
                'boletos.id_rifa' => $id,
                'boletos.estado' => Boletos::ESTADO_PAGADO,
                'boletos.is_deleted' => 0,
                'jugadores.is_deleted' => 0,
                'boleto_numeros.is_deleted' => 0
            ])
            ->groupBy(['jugadores.id', 'jugadores.nombre', 'jugadores.telefono'])
            ->orderBy(['total_numeros' => SORT_DESC])
            ->limit(3)
            ->all();

        // Convertir a objetos para la vista
        $topJugadoresObj = [];
        foreach ($topJugadores as $j) {
            $obj = new \stdClass();
            $obj->id = $j['id'];
            $obj->nombre = $j['nombre'];
            $obj->telefono = $j['telefono'];
            $obj->total_numeros = $j['total_numeros'];
            $topJugadoresObj[] = $obj;
        }

        $this->layout = 'admin-main';

        return $this->render('view', [
            'rifa' => $rifa,
            'boletos' => $boletos,
            'numerosJugados' => $numerosJugados,
            'porcentajeProgreso' => $porcentajeProgreso,
            'topJugadores' => $topJugadoresObj,
        ]);
    }

    /**
     * Crea una nueva rifa
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Rifas();
        $sorteo = new Sorteos();
        $premios = [];

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Cargar datos de la rifa
                $post = Yii::$app->request->post();
                $model->load($post);
                $model->id_operador_registro = Yii::$app->user->id;
                $model->estado = Rifas::ESTADO_BORRADOR;

                // Parsear fecha_inicio con hora
                // Auto-set fecha_inicio to current datetime
                $model->fecha_inicio = date('Y-m-d H:i:s');

                // Parsear fecha_fin con hora
                if (isset($post['fecha_fin_date']) && $post['fecha_fin_date']) {
                    $hour = (int) $post['fecha_fin_hour'];
                    $minute = $post['fecha_fin_minute'];
                    $ampm = $post['fecha_fin_ampm'];

                    // Convertir a formato 24 horas
                    if ($ampm == 'PM' && $hour != 12) {
                        $hour += 12;
                    } elseif ($ampm == 'AM' && $hour == 12) {
                        $hour = 0;
                    }

                    $model->fecha_fin = $post['fecha_fin_date'] . ' ' . sprintf('%02d:%s:00', $hour, $minute);
                }

                // Manejar imagen
                $imagenFile = UploadedFile::getInstanceByName('imagen');
                if ($imagenFile) {
                    $uploadPath = Yii::getAlias('@webroot/uploads/rifas/');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $fileName = 'rifa_' . time() . '_' . uniqid() . '.' . $imagenFile->extension;
                    if ($imagenFile->saveAs($uploadPath . $fileName)) {
                        $model->img = '/uploads/rifas/' . $fileName;
                    }
                }

                if (!$model->save()) {
                    throw new \Exception('Error al guardar la rifa: ' . implode(', ', $model->getFirstErrors()));
                }

                // Guardar sorteo
                $sorteo->load($post);
                $sorteo->id_rifa = $model->id;

                // Si el checkbox está marcado, usar fecha_fin como fecha_sorteo
                if (isset($post['fecha_sorteo_es_fin']) && $post['fecha_sorteo_es_fin']) {
                    $sorteo->fecha_sorteo = $model->fecha_fin;
                    $sorteo->descripcion = $model->descripcion;
                }

                if (!$sorteo->save()) {
                    throw new \Exception('Error al guardar el sorteo: ' . implode(', ', $sorteo->getFirstErrors()));
                }

                // Guardar premios
                if (isset($post['Premios']) && is_array($post['Premios'])) {
                    $orden = 1;
                    foreach ($post['Premios'] as $premioData) {
                        if (!empty($premioData['titulo'])) {
                            $premio = new Premios();
                            $premio->id_rifa = $model->id;
                            $premio->titulo = $premioData['titulo'];
                            $premio->descripcion = $premioData['descripcion'] ?? null;
                            $premio->valor_estimado = $premioData['valor_estimado'] ?? null;
                            $premio->orden = $orden++;
                            $premio->entregado = 0;

                            if (!$premio->save()) {
                                throw new \Exception('Error al guardar premio: ' . implode(', ', $premio->getFirstErrors()));
                            }
                        }
                    }
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Rifa creada exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $this->layout = 'admin-main';

        return $this->render('create', [
            'model' => $model,
            'sorteo' => $sorteo,
            'premios' => $premios,
        ]);
    }

    /**
     * Actualiza una rifa existente
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = Rifas::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('La rifa no existe.');
        }

        // Obtener sorteo existente o crear uno nuevo
        $sorteo = Sorteos::findOne(['id_rifa' => $id]) ?? new Sorteos();

        // Obtener premios existentes
        $premios = Premios::find()
            ->where(['id_rifa' => $id])
            ->orderBy(['orden' => SORT_ASC])
            ->all();

        // Restricciones de edición
        $today = date('Y-m-d');
        $canEditFechaInicio = ($model->fecha_inicio && $model->fecha_inicio > $today);

        if (Yii::$app->request->isPost) {
            $transaction = Yii::$app->db->beginTransaction();

            try {
                $post = Yii::$app->request->post();

                // Guardar valores originales para campos restringidos
                $originalSlug = $model->slug;
                $originalMaxNumeros = $model->max_numeros;
                $originalFechaInicio = $model->fecha_inicio;

                $model->load($post);

                // Restaurar campos restringidos
                $model->slug = $originalSlug;
                $model->max_numeros = $originalMaxNumeros;

                // Parsear fecha_inicio con hora (solo si se puede editar)


                // Parsear fecha_fin con hora
                if (isset($post['fecha_fin_date']) && $post['fecha_fin_date']) {
                    $hour = (int) $post['fecha_fin_hour'];
                    $minute = $post['fecha_fin_minute'];
                    $ampm = $post['fecha_fin_ampm'];

                    // Convertir a formato 24 horas
                    if ($ampm == 'PM' && $hour != 12) {
                        $hour += 12;
                    } elseif ($ampm == 'AM' && $hour == 12) {
                        $hour = 0;
                    }

                    $model->fecha_fin = $post['fecha_fin_date'] . ' ' . sprintf('%02d:%s:00', $hour, $minute);
                }

                // Manejar imagen
                $imagenFile = UploadedFile::getInstanceByName('imagen');
                if ($imagenFile) {
                    $uploadPath = Yii::getAlias('@webroot/uploads/rifas/');
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $fileName = 'rifa_' . time() . '_' . uniqid() . '.' . $imagenFile->extension;
                    if ($imagenFile->saveAs($uploadPath . $fileName)) {
                        $model->img = '/uploads/rifas/' . $fileName;
                    }
                }

                $model->updated_at = date('Y-m-d H:i:s');

                if (!$model->save()) {
                    throw new \Exception('Error al guardar la rifa: ' . implode(', ', $model->getFirstErrors()));
                }

                // Actualizar o crear sorteo
                $sorteo->load($post);
                $sorteo->id_rifa = $model->id;

                // Si el checkbox está marcado, usar fecha_fin como fecha_sorteo
                if (isset($post['fecha_sorteo_es_fin']) && $post['fecha_sorteo_es_fin']) {
                    $sorteo->fecha_sorteo = $model->fecha_fin;
                    $sorteo->descripcion = $model->descripcion;
                } else {
                    // Validar que fecha_sorteo no esté en el rango de la rifa
                    if ($sorteo->fecha_sorteo && $model->fecha_inicio && $model->fecha_fin) {
                        if ($sorteo->fecha_sorteo >= $model->fecha_inicio && $sorteo->fecha_sorteo <= $model->fecha_fin) {
                            throw new \Exception('La fecha del sorteo debe ser posterior a la fecha de finalización de la rifa.');
                        }
                    }
                }

                if (!$sorteo->save()) {
                    throw new \Exception('Error al guardar el sorteo: ' . implode(', ', $sorteo->getFirstErrors()));
                }

                // Procesar premios
                if (isset($post['Premios']) && is_array($post['Premios'])) {
                    $premiosIds = [];
                    $orden = 1;

                    foreach ($post['Premios'] as $premioData) {
                        if (!empty($premioData['titulo'])) {
                            // Si tiene ID, actualizar; si no, crear nuevo
                            if (!empty($premioData['id'])) {
                                $premio = Premios::findOne($premioData['id']);
                                if (!$premio || $premio->id_rifa != $model->id) {
                                    throw new \Exception('Premio no válido.');
                                }
                            } else {
                                $premio = new Premios();
                                $premio->id_rifa = $model->id;
                                $premio->entregado = 0;
                            }

                            $premio->titulo = $premioData['titulo'];
                            $premio->descripcion = $premioData['descripcion'] ?? null;
                            $premio->valor_estimado = $premioData['valor_estimado'] ?? null;
                            $premio->orden = $orden++;

                            if (!$premio->save()) {
                                throw new \Exception('Error al guardar premio: ' . implode(', ', $premio->getFirstErrors()));
                            }

                            $premiosIds[] = $premio->id;
                        }
                    }

                    // No eliminamos premios existentes que no estén en la lista
                    // solo añadimos nuevos (según requerimiento)
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Rifa actualizada exitosamente.');
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        $this->layout = 'admin-main';

        return $this->render('update', [
            'model' => $model,
            'sorteo' => $sorteo,
            'premios' => $premios,
        ]);
    }

    /**
     * Activa una rifa (cambia de borrador a activa)
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionActivar($id)
    {
        $model = Rifas::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('La rifa no existe.');
        }

        // Validar que la rifa esté en estado borrador
        if ($model->estado !== Rifas::ESTADO_BORRADOR) {
            Yii::$app->session->setFlash('error', 'Solo se pueden activar rifas en estado borrador.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Validar que tenga todos los datos necesarios
        if (!$model->titulo || !$model->fecha_inicio || !$model->fecha_fin || !$model->max_numeros || !$model->precio_boleto) {
            Yii::$app->session->setFlash('error', 'La rifa debe tener todos los datos básicos (título, fechas, números y precio) antes de activarse.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Validar que tenga al menos un sorteo asociado
        $sorteo = Sorteos::findOne(['id_rifa' => $id]);
        if (!$sorteo || !$sorteo->fecha_sorteo) {
            Yii::$app->session->setFlash('error', 'La rifa debe tener una fecha de sorteo configurada antes de activarse.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Cambiar estado a activa
        $model->estado = Rifas::ESTADO_ACTIVA;
        $model->updated_at = date('Y-m-d H:i:s');

        if ($model->save()) {
            Yii::$app->session->setFlash('success', '¡Rifa activada exitosamente! Ahora está visible para los usuarios.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al activar la rifa: ' . implode(', ', $model->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Busca un número jugado por su valor (AJAX)
     * @param int $id ID de la rifa
     * @param string $numero Número a buscar
     * @return array
     */
    public function actionBuscarNumero($id, $numero = '')
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (empty($numero)) {
            return ['success' => false, 'message' => 'Ingrese un número'];
        }

        // Buscar números que coincidan (solo boletos pagados)
        $resultados = BoletoNumeros::find()
            ->alias('bn')
            ->select([
                'bn.id',
                'bn.numero',
                'bn.id_boleto',
                'b.codigo as boleto_codigo',
                'j.nombre as jugador_nombre',
                'j.telefono as jugador_telefono',
            ])
            ->innerJoin('boletos b', 'b.id = bn.id_boleto')
            ->innerJoin('jugadores j', 'j.id = b.id_jugador')
            ->where([
                'b.id_rifa' => $id,
                'b.estado' => Boletos::ESTADO_PAGADO,
                'b.is_deleted' => 0,
                'bn.is_deleted' => 0,
            ])
            ->andWhere(['like', 'bn.numero', $numero])
            ->limit(20)
            ->asArray()
            ->all();

        return [
            'success' => true,
            'resultados' => $resultados,
        ];
    }

    /**
     * Establece un número como ganador
     * @param int $id ID de la rifa
     * @return \yii\web\Response|array
     */
    public function actionSetGanador($id)
    {
        $rifa = Rifas::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$rifa) {
            throw new \yii\web\NotFoundHttpException('La rifa no existe.');
        }

        if ($rifa->estado !== Rifas::ESTADO_ACTIVA) {
            Yii::$app->session->setFlash('error', 'Solo se puede establecer ganador en rifas activas.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $post = Yii::$app->request->post();
        $boletoNumeroId = $post['boleto_numero_id'] ?? null;
        $premioId = $post['premio_id'] ?? null;

        if (!$boletoNumeroId) {
            Yii::$app->session->setFlash('error', 'Debe seleccionar un número ganador.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Obtener el número del boleto
        $boletoNumero = BoletoNumeros::findOne($boletoNumeroId);
        if (!$boletoNumero || $boletoNumero->is_deleted) {
            Yii::$app->session->setFlash('error', 'El número seleccionado no es válido.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Verificar que pertenece a esta rifa
        $boleto = Boletos::findOne($boletoNumero->id_boleto);
        if (!$boleto || $boleto->id_rifa != $id) {
            Yii::$app->session->setFlash('error', 'El número no pertenece a esta rifa.');
            return $this->redirect(['view', 'id' => $id]);
        }

        // Obtener el sorteo
        $sorteo = Sorteos::findOne(['id_rifa' => $id]);
        if (!$sorteo) {
            // Crear sorteo si no existe
            $sorteo = new Sorteos();
            $sorteo->id_rifa = $id;
            $sorteo->fecha_sorteo = date('Y-m-d H:i:s');
            $sorteo->descripcion = 'Sorteo de ' . $rifa->titulo;
            $sorteo->save();
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Crear registro de ganador
            // El trigger trg_sorteos_ganadores_after_insert_boleto cambiará el estado del boleto a 'ganador'
            // El trigger trg_sorteos_ganadores_after_insert_rifa cambiará el estado de la rifa a 'sorteada'
            $ganador = new \app\models\SorteosGanadores();
            $ganador->id_sorteo = $sorteo->id;
            $ganador->id_boleto = $boleto->id;
            $ganador->id_premio = $premioId ?: null;
            $ganador->numero_ganador = $boletoNumero->numero;

            if (!$ganador->save()) {
                throw new \Exception('Error al registrar ganador: ' . implode(', ', $ganador->getFirstErrors()));
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', '¡Ganador registrado exitosamente! El número ' . $boletoNumero->numero . ' ha sido marcado como ganador.');

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $id]);
    }
}
