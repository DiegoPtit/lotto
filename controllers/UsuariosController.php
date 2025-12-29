<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Usuarios;
use app\models\Roles;

/**
 * UsuariosController maneja la administración de usuarios del sistema
 */
class UsuariosController extends Controller
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
     * Lista todos los usuarios
     * @return string
     */
    public function actionIndex()
    {
        $rolFiltro = Yii::$app->request->get('rol', null);

        $query = Usuarios::find()
            ->with(['rol'])
            ->where(['is_deleted' => 0])
            ->orderBy(['created_at' => SORT_DESC]);

        if ($rolFiltro) {
            $query->andWhere(['rol_id' => $rolFiltro]);
        }

        $usuarios = $query->all();
        $roles = Roles::find()->all();

        $this->layout = 'admin-main';

        return $this->render('index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'rolFiltro' => $rolFiltro,
        ]);
    }

    /**
     * Muestra los detalles de un usuario
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
     * Crea un nuevo usuario
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Usuarios();
        $roles = Roles::find()->all();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->nombre = $post['nombre'] ?? '';
            $model->correo = $post['correo'] ?? '';
            $model->rol_id = $post['rol_id'] ?? 1;

            // Establecer contraseña si se proporciona
            if (!empty($post['password'])) {
                $model->setPassword($post['password']);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuario creado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al crear: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('create', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    /**
     * Actualiza un usuario existente
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $roles = Roles::find()->all();

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();

            $model->nombre = $post['nombre'] ?? $model->nombre;
            $model->correo = $post['correo'] ?? $model->correo;
            $model->rol_id = $post['rol_id'] ?? $model->rol_id;

            // Actualizar contraseña solo si se proporciona una nueva
            if (!empty($post['password'])) {
                $model->setPassword($post['password']);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Usuario actualizado correctamente.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Error al actualizar: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        $this->layout = 'admin-main';

        return $this->render('update', [
            'model' => $model,
            'roles' => $roles,
        ]);
    }

    /**
     * Elimina un usuario (soft delete)
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // No permitir eliminar usuario actual
        if ($model->id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'No puedes eliminar tu propio usuario.');
            return $this->redirect(['index']);
        }

        $model->is_deleted = 1;
        $model->deleted_at = date('Y-m-d H:i:s');

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'Usuario eliminado correctamente.');
        } else {
            Yii::$app->session->setFlash('error', 'Error al eliminar el usuario.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Busca el modelo Usuarios por su ID
     * @param int $id
     * @return Usuarios
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Usuarios::findOne(['id' => $id, 'is_deleted' => 0]);

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El usuario no existe.');
        }

        return $model;
    }
}
