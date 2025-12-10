<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $mejoresRifas = \app\models\Rifas::getMejoresRifas(5);

        return $this->render('index', [
            'mejoresRifas' => $mejoresRifas,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/panel/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/panel/index']);
        }

        $model->password = '';
        $signupModel = new \app\models\SignupForm();

        return $this->render('login', [
            'model' => $model,
            'signupModel' => $signupModel,
        ]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $model = new \app\models\SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Registro exitoso. Por favor inicie sesión.');
            return $this->redirect(['login']);
        }

        return $this->render('login', [
            'model' => new LoginForm(),
            'signupModel' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Busca tickets por cédula de jugador
     * @return string
     */
    public function actionBuscarTickets()
    {
        $request = Yii::$app->request;
        if (!$request->isAjax) {
            return '';
        }

        $cedula = $request->post('cedula');
        if (empty($cedula)) {
            return '<div class="alert alert-warning">Por favor ingrese un número de cédula.</div>';
        }

        $jugador = \app\models\Jugadores::findOne(['cedula' => $cedula, 'is_deleted' => 0]);

        $boletosPorRifa = [];

        if ($jugador) {
            // Buscar boletos pagados o reservados
            $boletos = \app\models\Boletos::find()
                ->where(['id_jugador' => $jugador->id, 'is_deleted' => 0])
                ->andWhere(['in', 'estado', [\app\models\Boletos::ESTADO_PAGADO, \app\models\Boletos::ESTADO_RESERVADO]])
                ->with(['rifa', 'boletoNumeros'])
                ->all();

            // Agrupar por Rifa
            foreach ($boletos as $boleto) {
                $rifaId = $boleto->id_rifa;
                if (!isset($boletosPorRifa[$rifaId])) {
                    $boletosPorRifa[$rifaId] = [
                        'rifa' => $boleto->rifa,
                        'boletos' => []
                    ];
                }
                $boletosPorRifa[$rifaId]['boletos'][] = $boleto;
            }
        }

        return $this->renderPartial('_tickets_results', [
            'boletosPorRifa' => $boletosPorRifa
        ]);
    }

    /**
     * Displays politics page.
     *
     * @param string $type Type of politics page (terms, privacy, cookies)
     * @return string
     */
    public function actionPolitics($type = 'terms')
    {
        $validTypes = ['terms', 'privacy', 'cookies'];
        if (!in_array($type, $validTypes)) {
            $type = 'terms';
        }

        return $this->render('politics', [
            'type' => $type,
        ]);
    }
}
