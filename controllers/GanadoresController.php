<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\SorteosGanadores;
use app\models\Rifas;

/**
 * GanadoresController maneja el histórico de ganadores del sistema
 */
class GanadoresController extends Controller
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
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lista todos los ganadores
     * @return string
     */
    public function actionIndex()
    {
        $rifaFiltro = Yii::$app->request->get('rifa', null);

        $query = SorteosGanadores::find()
            ->with(['sorteo', 'sorteo.rifa', 'boleto', 'boleto.jugador', 'premio'])
            ->joinWith('sorteo.rifa')
            ->orderBy(['sorteos_ganadores.created_at' => SORT_DESC]);

        if ($rifaFiltro) {
            $query->andWhere(['rifas.id' => $rifaFiltro]);
        }

        $ganadores = $query->all();

        // Obtener rifas sorteadas para el filtro
        $rifasSorteadas = Rifas::find()
            ->where(['estado' => Rifas::ESTADO_SORTEADA])
            ->orderBy(['titulo' => SORT_ASC])
            ->all();

        $this->layout = 'admin-main';

        return $this->render('index', [
            'ganadores' => $ganadores,
            'rifasSorteadas' => $rifasSorteadas,
            'rifaFiltro' => $rifaFiltro,
        ]);
    }

    /**
     * Muestra los detalles de un ganador
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
     * Busca el modelo SorteosGanadores por su ID
     * @param int $id
     * @return SorteosGanadores
     * @throws \yii\web\NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = SorteosGanadores::findOne($id);

        if ($model === null) {
            throw new \yii\web\NotFoundHttpException('El registro de ganador no existe.');
        }

        return $model;
    }
}
