<?php

namespace app\controllers;

use app\models\apis\SocialMediaApi;
use app\models\SocialMedia;
use Yii;
use app\models\Event;
use yii\base\Model;
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
        $searchModel = new SearchEventForm();

        if ($searchModel->load(Yii::$app->request->get()) && $searchModel->validate()) { // search form
            $query = Event::find()->where(['like', 'name', $searchModel->name]);
            if (isset($searchModel->latitude)) {
                $query = $query->andWhere('acos(sin('.$searchModel->latitude . ') * sin(Latitude) + cos('.$searchModel->latitude . ') * cos(Latitude) * cos(Longitude - ('.$searchModel->longitude . '))) * 6371 <= ' .
                $searchModel->radius);
            }
            echo $searchModel->latitude;
            if ($searchModel->from_date !== "") {
                $query = $query->andWhere(['>=', 'UNIX_TIMESTAMP(start_date)', strtotime($searchModel->from_date)]);
            }
            if ($searchModel->to_date !== "") {
                $query = $query->andWhere(['<=', 'UNIX_TIMESTAMP(start_date)', strtotime($searchModel->to_date)]);
            }
        } else { // normal index call
            $query = Event::find()->where(['>=', 'UNIX_TIMESTAMP(start_date)', time()]);
        }

        $query = $query->joinWith('user');

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        // Event List ordered by date
        $eventList = $query->orderBy('start_date')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        // Top Events
        $topList = $query->orderBy(['clicks' => SORT_DESC])->limit(3)->all();

        // New Events
        $newList = $query->orderBy(['creation_date' => SORT_DESC])->limit(3)->all();

        return $this->render('index', ['searchModel' => $searchModel,
            'eventList' => $eventList,
            'pagination' => $pagination, 'topList' => $topList, 'newList' => $newList]);

    }

    /**
     * Displays a single Event model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();
        //Yii::$app->cache->delete('socialmedia' . $id);
        if (!Yii::$app->cache->get('socialmedia' . $id)) {
            $socialMediaApi = new SocialMediaApi();
            foreach ($socialMediaModels as $socialMediaModel) {
                $socialMediaApi->loadSocialMedia($socialMediaModel);
            }
            Yii::$app->cache->set('socialmedia' . $id, $socialMediaApi->getSocialMedia());
        }

        ++$model->clicks;
        $model->update();

        return $this->render('view', [
            'model' => $model, 'socialmedia' => Yii::$app->cache->get('socialmedia' . $id),
            'socialMediaModels' => $socialMediaModels,
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

        if (!$model->num_socialMedia) {
            $model->num_socialMedia = 1;
        }

        $socialMediaModels = [];

        for ($i = 0; $i < $model->num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();
        $valid = true;

        if ($model->load($postData) && $model->validate()) {
            foreach ($socialMediaModels as $socialMediaModel) {
                if ($socialMediaModel->load($postData) && $socialMediaModel->validate()) {
                    var_dump($socialMediaModel->url);
                } else {
                    $valid = false;
                }
            }
        } else {
            $valid = false;
        }

        if ($valid) {
            $model->user_id = Yii::$app->user->id;
            $model->start_date = date('Y-m-d H:i:s', strtotime($model->start_date));
            $model->end_date = date('Y-m-d H:i:s', strtotime($model->end_date));

            $model->clicks = 0;

            // image
            $image = $model->uploadImage();
            if ($model->save()) {
                if ($image !== null) {
                    $image->saveAs($model->getImagePath());
                }

                foreach ($socialMediaModels as $socialMediaModel) {
                    if ($socialMediaModel->url === '') {
                        //$socialMediaModel->delete();
                    } else {
                        $socialMediaModel->event_id = $model->id;
                        $socialMediaModel->save();
                    }
                }

                //return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
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
        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();

        if (!$model->num_socialMedia) {
            $model->num_socialMedia = 1;
        }

        for ($i = 0; $i < $model->num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();
        $valid = true;

        $old_image = $model->image;

        if ($model->load($postData) && $model->validate()) {
            foreach ($socialMediaModels as $socialMediaModel) {
                if ($socialMediaModel->load($postData) && $socialMediaModel->validate()) {

                } else {
                    $valid = false;
                }
            }
        } else {
            $valid = false;
        }

        if ($valid) {
            $model->start_date = date('Y-m-d H:i:s', strtotime($model->start_date));
            $model->end_date = date('Y-m-d H:i:s', strtotime($model->end_date));

            // image
            $image = $model->uploadImage();

            if ($image === null) {
                $model->image = $old_image;
            }

            if ($model->update() !== false) {
                if ($image !== null) { // delete old and overwrite
                    $model->deleteImage($old_image);
                    $image->saveAs($model->getImagePath());
                }
                foreach ($socialMediaModels as $socialMediaModel) {
                    if ($socialMediaModel->url === '') {
                        $socialMediaModel->delete();
                    } else {
                        $socialMediaModel->event_id = $model->id;
                        $socialMediaModel->save();
                    }
                }
                Yii::$app->cache->delete('socialmedia' . $id); // delete cache of this event
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        } else {
            $model->start_date = date('d.m.Y G:i', strtotime($model->start_date));
            $model->end_date = date('d.m.Y G:i', strtotime($model->end_date));

        }
        return $this->render('update', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
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