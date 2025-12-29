<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use app\models\Testimonios;
use app\models\Comentarios;

class TestimoniosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete', 'index', 'delete-comment'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete', 'index', 'delete-comment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Deshabilitar CSRF para acciones AJAX
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['like', 'add-comment', 'like-comment', 'delete-comment'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Lista de testimonios para el panel administrativo
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'admin-main';

        $testimonios = Testimonios::find()
            ->where(['is_deleted' => 0])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'testimonios' => $testimonios,
        ]);
    }

    /**
     * Vista pública de un testimonio individual
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $testimonio = $this->findModel($id);

        $comentarios = Comentarios::find()
            ->where(['id_testimonio' => $id, 'is_deleted' => 0])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('view', [
            'testimonio' => $testimonio,
            'comentarios' => $comentarios,
        ]);
    }

    /**
     * Crear un nuevo testimonio
     * @return string|Response
     */
    public function actionCreate()
    {
        $this->layout = 'admin-main';
        $model = new Testimonios();

        if ($model->load(Yii::$app->request->post())) {
            // Manejo de archivo multimedia
            $file = UploadedFile::getInstance($model, 'url_media');
            if ($file) {
                $uploadsDir = Yii::getAlias('@webroot/uploads/testimonios');
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }
                $fileName = time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $file->extension;
                $filePath = $uploadsDir . '/' . $fileName;
                if ($file->saveAs($filePath)) {
                    $model->url_media = '/uploads/testimonios/' . $fileName;
                }
            }

            $model->contador_likes = 0;
            $model->created_at = date('Y-m-d H:i:s');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Testimonio creado correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Actualizar un testimonio existente
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $this->layout = 'admin-main';
        $model = $this->findModel($id);
        $oldMediaUrl = $model->url_media;

        if ($model->load(Yii::$app->request->post())) {
            // Manejo de archivo multimedia
            $file = UploadedFile::getInstance($model, 'url_media');
            if ($file) {
                $uploadsDir = Yii::getAlias('@webroot/uploads/testimonios');
                if (!is_dir($uploadsDir)) {
                    mkdir($uploadsDir, 0777, true);
                }
                $fileName = time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $file->extension;
                $filePath = $uploadsDir . '/' . $fileName;
                if ($file->saveAs($filePath)) {
                    $model->url_media = '/uploads/testimonios/' . $fileName;
                }
            } else {
                // Mantener el archivo anterior si no se subió uno nuevo
                $model->url_media = $oldMediaUrl;
            }

            $model->updated_at = date('Y-m-d H:i:s');

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Testimonio actualizado correctamente.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Eliminar un testimonio (soft delete) - AJAX
     * @return array
     */
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado'];
        }

        $model = $this->findModel($id);
        $model->is_deleted = 1;
        $model->deleted_at = date('Y-m-d H:i:s');

        if ($model->save(false)) {
            return ['success' => true, 'message' => 'Testimonio eliminado correctamente'];
        }

        return ['success' => false, 'message' => 'Error al eliminar el testimonio'];
    }

    /**
     * Dar like a un testimonio - AJAX
     * @return array
     */
    public function actionLike()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado'];
        }

        $model = $this->findModel($id);
        $model->contador_likes = ($model->contador_likes ?? 0) + 1;

        if ($model->save(false)) {
            return ['success' => true, 'likes' => $model->contador_likes];
        }

        return ['success' => false, 'message' => 'Error al dar like'];
    }

    /**
     * Agregar comentario a un testimonio - AJAX
     * @return array
     */
    public function actionAddComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $testimonioId = Yii::$app->request->post('id_testimonio');
        $nombre = Yii::$app->request->post('nombre');
        $mensaje = Yii::$app->request->post('mensaje');

        if (!$testimonioId || !$nombre || !$mensaje) {
            return ['success' => false, 'message' => 'Datos incompletos'];
        }

        $comentario = new Comentarios();
        $comentario->id_testimonio = $testimonioId;
        $comentario->nombre = $nombre;
        $comentario->mensaje = $mensaje;
        $comentario->contador_likes = 0;
        $comentario->is_deleted = 0;
        $comentario->created_at = date('Y-m-d H:i:s');

        if ($comentario->save()) {
            return [
                'success' => true,
                'comentario' => [
                    'id' => $comentario->id,
                    'nombre' => $comentario->nombre,
                    'mensaje' => $comentario->mensaje,
                    'contador_likes' => $comentario->contador_likes,
                    'created_at' => Yii::$app->formatter->asRelativeTime($comentario->created_at),
                ]
            ];
        }

        return ['success' => false, 'message' => 'Error al guardar el comentario'];
    }

    /**
     * Dar like a un comentario - AJAX
     * @return array
     */
    public function actionLikeComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado'];
        }

        $comentario = Comentarios::findOne($id);
        if (!$comentario || $comentario->is_deleted) {
            return ['success' => false, 'message' => 'Comentario no encontrado'];
        }

        $comentario->contador_likes = ($comentario->contador_likes ?? 0) + 1;

        if ($comentario->save(false)) {
            return ['success' => true, 'likes' => $comentario->contador_likes];
        }

        return ['success' => false, 'message' => 'Error al dar like'];
    }

    /**
     * Eliminar comentario (soft delete) - AJAX - Solo admins
     * @return array
     */
    public function actionDeleteComment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (!$id) {
            return ['success' => false, 'message' => 'ID no proporcionado'];
        }

        $comentario = Comentarios::findOne($id);
        if (!$comentario) {
            return ['success' => false, 'message' => 'Comentario no encontrado'];
        }

        $comentario->is_deleted = 1;
        $comentario->deleted_at = date('Y-m-d H:i:s');

        if ($comentario->save(false)) {
            return ['success' => true, 'message' => 'Comentario eliminado correctamente'];
        }

        return ['success' => false, 'message' => 'Error al eliminar el comentario'];
    }

    /**
     * Busca el modelo Testimonios por su ID
     * @param int $id
     * @return Testimonios
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Testimonios::findOne(['id' => $id, 'is_deleted' => 0]);
        if ($model === null) {
            throw new NotFoundHttpException('El testimonio solicitado no existe.');
        }
        return $model;
    }
}
