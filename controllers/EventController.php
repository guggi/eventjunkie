<?php

namespace app\controllers;

use app\models\apis\SocialMediaApi;
use Yii;
use app\models\Event;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\data\Pagination;
use app\models\SearchEventForm;
use yii\web\UploadedFile;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = Event::find();

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $eventList = $query->orderBy('start_date')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();


        $searchModel = new SearchEventForm();

        return $this->render('index', ['searchModel' => $searchModel,
            'eventList' => $eventList,
            'pagination' => $pagination,]);
    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $socialmediapi = new SocialMediaApi();
        $socialmediapi->loadSocialMedia($model->flickr);
        $socialmedia = $socialmediapi->getSocialMedia();

        return $this->render('view', [
            'model' => $model, 'socialmedia' => $socialmedia
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        if (Yii::$app->user->isGuest) {
            $this->redirect(\Yii::$app->request->BaseUrl . '/index.php?r=user/login');
        }

        $model = new Event();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->user_id = Yii::$app->user->id;
            $model->start_date = date('Y-m-d H:i:s', strtotime($model->start_date));

            if (date('Y-m-d H:i:s', strtotime($model->end_date)) == "1970-01-01 00:00:00") {
                $model->end_date = date('Y-m-d H:i:s', strtotime($model->start_date));
            } else {
                $model->end_date = date('Y-m-d H:i:s', strtotime($model->end_date));
            }

            $model->clicks = 0;

            // image
            $image = $model->uploadImage();
            if ($model->save()) {
                if ($image !== null) {
                    $image->saveAs($model->getImagePath());
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create', ['model' => $model]);
            }
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_image = $model->image;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->start_date = date('Y-m-d H:i:s', strtotime($model->start_date));

            if (date('Y-m-d H:i:s', strtotime($model->end_date)) == "1970-01-01 00:00:00") {
                $model->end_date = date('Y-m-d H:i:s', strtotime($model->start_date));
            } else {
                $model->end_date = date('Y-m-d H:i:s', strtotime($model->end_date));
            }

            // image
            $image = $model->uploadImage();

            if ($image === null) {
                $model->image = $old_image;
            }

            if ($model->save()) {
                if ($image !== null) { // delete old and overwrite
                    $model->deleteImage($old_image);
                    $image->saveAs($model->getImagePath());
                }
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->deleteImage($model->image);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

/*
            if ($model->address !== '') {
                $parsed_address = str_replace(' ', '+', $model->address);
                $jsonData = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' .
                    $parsed_address . '&sensor=true');
                $data = json_decode($jsonData);
                if ($data->{'status'} != 'OK') {
                    Yii::$app->session->setFlash('error', 'Address doesn\'t exist.');
                    return $this->render('create', ['model' => $model]);
                }
                $model->latitude = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $model->longitude = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            }*/