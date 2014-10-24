<?php

namespace app\controllers;

use app\models\SearchEventForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\CreateEventForm;
use yii\data\Pagination;
use app\models\Event;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
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
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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

    public function actionIndex()
    {
        $query = Event::find();

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $eventList = $query->orderBy('eventDate')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();


        $searchModel = new SearchEventForm();

        return $this->render('index', ['searchModel' => $searchModel,
            'eventList' => $eventList,
            'pagination' => $pagination,]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionCreateEvent()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new CreateEventForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $jsonData  = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=wien&sensor=true");
            $data = json_decode($jsonData);
            $xlat = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $xlong = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};


            return $this->render('createEvent-confirm', ['model' => $model, 'xlat' => $xlat]);
        } else {
            return $this->render("createEvent", ["model" => $model]);
        }

    }
}
