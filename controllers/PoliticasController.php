<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Politicas;

/**
 * PoliticasController maneja la administración de políticas del sistema
 */
class PoliticasController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lista todas las políticas
     * @return string
     */
    public function actionIndex()
    {
        $tipoFiltro = Yii::$app->request->get('tipo', null);

        $query = Politicas::find()
            ->orderBy(['tipo' => SORT_ASC, 'created_at' => SORT_DESC]);

        if ($tipoFiltro) {
            $query->andWhere(['tipo' => $tipoFiltro]);
        }

        $politicas = $query->all();

        // Agrupar por tipo
        $politicasPorTipo = [];
        foreach ($politicas as $politica) {
            $politicasPorTipo[$politica->tipo][] = $politica;
        }

        $this->layout = 'admin-main';

        return $this->render('index', [
            'politicas' => $politicas,
            'politicasPorTipo' => $politicasPorTipo,
            'tipoFiltro' => $tipoFiltro,
            'tipos' => Politicas::optsTipo(),
        ]);
    }

    /**
     * Muestra los detalles de una política
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $this->layout = 'admin-main';

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Crea una nueva política
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Politicas();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->tipo = $post['tipo'] ?? Politicas::TIPO_OTRO;
            $model->titulo = $post['titulo'] ?? '';
            $model->descripcion = $post['descripcion'] ?? '';
            $model->id_operador_registro = Yii::$app->user->id;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Política creada correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al crear: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('create', [
            'model' => $model,
            'tipos' => Politicas::optsTipo(),
        ]);
    }

    /**
     * Actualiza una política existente
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->tipo = $post['tipo'] ?? $model->tipo;
            $model->titulo = $post['titulo'] ?? $model->titulo;
            $model->descripcion = $post['descripcion'] ?? $model->descripcion;

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Política actualizada correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('update', [
            'model' => $model,
            'tipos' => Politicas::optsTipo(),
        ]);
    }

    /**
     * Elimina una política
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Política eliminada correctamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al eliminar la política.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Busca el modelo Politicas por su ID
     * @param int $id
     * @return Politicas
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Politicas::findOne($id);

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('La política no existe.');
        }

        return $model;
    }
}
