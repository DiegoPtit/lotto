<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\MetodosPago;
use app\models\MetodosPagoTipo;

/**
 * MetodosPagoController maneja la administración de métodos de pago
 */
class MetodosPagoController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'create-tipo'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'create-tipo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lista todos los métodos de pago
     * @return string
     */
    public function actionIndex()
    {
        $metodosPago = MetodosPago::find()
            ->with(['tipo'])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $this->layout = 'admin-main';

        return $this->render('index', [
            'metodosPago' => $metodosPago,
        ]);
    }

    /**
     * Muestra los detalles de un método de pago
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionView($id)
    {
        $model = MetodosPago::findOne($id);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('El método de pago no existe.');
        }

        // Procesar actualización
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            // Obtener los campos disponibles del tipo
            $tipo = $model->tipo;

            // Actualizar solo los campos que están marcados
            if ($tipo->has_banco && isset($post['banco'])) {
                $model->banco = $post['banco'];
            }
            if ($tipo->has_titular && isset($post['titular'])) {
                $model->titular = $post['titular'];
            }
            if ($tipo->has_cedula && isset($post['cedula'])) {
                $model->cedula = $post['cedula'];
            }
            if ($tipo->has_telefono && isset($post['telefono'])) {
                $model->telefono = $post['telefono'];
            }
            if ($tipo->has_nro_cuenta && isset($post['nro_cuenta'])) {
                $model->nro_cuenta = $post['nro_cuenta'];
            }

            // Actualizar visibilidad
            if (isset($post['visibilidad'])) {
                $model->visibilidad = $post['visibilidad'];
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Método de pago actualizado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Crea un nuevo método de pago
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new MetodosPago();
        $tipos = MetodosPagoTipo::find()->all();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->tipo_id = $post['tipo_id'] ?? null;
            $model->id_operador_registro = Yii::$app->user->id;
            $model->visibilidad = $post['visibilidad'] ?? 'publica';

            // Obtener el tipo para saber qué campos llenar
            $tipo = MetodosPagoTipo::findOne($model->tipo_id);

            if ($tipo) {
                if ($tipo->has_banco) {
                    $model->banco = $post['banco'] ?? null;
                }
                if ($tipo->has_titular) {
                    $model->titular = $post['titular'] ?? null;
                }
                if ($tipo->has_cedula) {
                    $model->cedula = $post['cedula'] ?? null;
                }
                if ($tipo->has_telefono) {
                    $model->telefono = $post['telefono'] ?? null;
                }
                if ($tipo->has_nro_cuenta) {
                    $model->nro_cuenta = $post['nro_cuenta'] ?? null;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Método de pago creado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al crear: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('create', [
            'model' => $model,
            'tipos' => $tipos,
        ]);
    }

    /**
     * Crea un nuevo tipo de método de pago via AJAX
     * @return array
     */
    public function actionCreateTipo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $post = Yii::$app->request->post();

        $tipo = new MetodosPagoTipo();
        $tipo->descripcion = $post['descripcion'] ?? '';
        $tipo->has_banco = isset($post['has_banco']) && $post['has_banco'] ? 1 : 0;
        $tipo->has_titular = isset($post['has_titular']) && $post['has_titular'] ? 1 : 0;
        $tipo->has_cedula = isset($post['has_cedula']) && $post['has_cedula'] ? 1 : 0;
        $tipo->has_telefono = isset($post['has_telefono']) && $post['has_telefono'] ? 1 : 0;
        $tipo->has_nro_cuenta = isset($post['has_nro_cuenta']) && $post['has_nro_cuenta'] ? 1 : 0;

        if ($tipo->save()) {
            return [
                'success' => true,
                'tipo' => [
                    'id' => $tipo->id,
                    'descripcion' => $tipo->descripcion,
                    'has_banco' => $tipo->has_banco,
                    'has_titular' => $tipo->has_titular,
                    'has_cedula' => $tipo->has_cedula,
                    'has_telefono' => $tipo->has_telefono,
                    'has_nro_cuenta' => $tipo->has_nro_cuenta,
                ],
            ];
        } else {
            return [
                'success' => false,
                'error' => implode(', ', $tipo->getFirstErrors()),
            ];
        }
    }

    /**
     * Elimina un método de pago
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = MetodosPago::findOne($id);

        if (!$model) {
            throw new \yii\web\NotFoundHttpException('El método de pago no existe.');
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Método de pago eliminado correctamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al eliminar el método de pago.');
        }

        return $this->redirect(['index']);
    }
}
