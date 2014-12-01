<?php

namespace app\controllers;

use app\models\apis\SocialMediaApi;
use app\models\SocialMedia;
use Yii;
use app\models\Event;
use yii\base\Exception;
use yii\base\Model;
use yii\db\IntegrityException;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],];
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
        $this->findSocialMedia($id, $socialMediaModels);

        ++$model->clicks;

        $model->update();

        // make "linked" link for hashtags
        foreach ($socialMediaModels as $socialMediaModel) {
            if ($socialMediaModel->site_name === 'twitter') {
                $url_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
                if (!preg_match($url_regex, $socialMediaModel->url)) {
                    $socialMediaModel->url = preg_replace(
                        '/#?(\w+)/i',
                        'https://twitter.com/hashtag/$1?src=hash',
                        $socialMediaModel->url);
                }
            }
        }

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
        $model = new Event();

        $socialMediaModels = [];

        if (!$model->num_socialMedia) {
            $model->num_socialMedia = 5;
        }

        for ($i = 0; $i < $model->num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();

        if ($model->load($postData) && $model->validate() && Model::loadMultiple($socialMediaModels, $postData) && Model::validateMultiple($socialMediaModels)) {
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
                        $socialMediaModel->delete();
                    } else {
                        $socialMediaModel->event_id = $model->id;
                        try {
                            $socialMediaModel->save();
                        } catch (IntegrityException $e) {
                            throw new \InvalidArgumentException('Url/Hashtag already exists');
                        }
                    }
                }

                // delete cache of this event and create new cache
                Yii::$app->cache->delete('socialmedia' . $model->id);
                $socialMediaModels = SocialMedia::find()->where(['event_id' => $model->id])->orderBy('id')->all();
                $this->findSocialMedia($model->id, $socialMediaModels);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
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

        if ($model->user_id !== Yii::$app->user->id) {
            return $this->redirect(['view', 'id' => $id]);
        }

        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();

        if (!$model->num_socialMedia) {
            $model->num_socialMedia = 1;
        }

        for ($i = 0; $i < $model->num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();

        $old_image = $model->image;

        if ($model->load($postData) && $model->validate() && Model::loadMultiple($socialMediaModels, $postData) && Model::validateMultiple($socialMediaModels)) {
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
                        try {
                            $socialMediaModel->save();
                        } catch (IntegrityException $e) {
                            throw new \InvalidArgumentException('Url/Hashtag already exists');
                        }
                    }
                }

                // delete cache of this event and create new cache
                Yii::$app->cache->delete('socialmedia' . $id);
                $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();
                $this->findSocialMedia($id, $socialMediaModels);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->start_date = date('d.m.Y G:i', strtotime($model->start_date));
            $model->end_date = date('d.m.Y G:i', strtotime($model->end_date));

        }
        return $this->render('update', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
    }


    /**
     * Adds new field.
     * @return mixed
     */
    public function actionAddField()
    {
        //todo funktioniert nicht
        $postData = Yii::$app->request->post();
        $model = new Event();
        $socialMediaModels = [];
        $model->load($postData);
        Model::loadMultiple($socialMediaModels, $postData);
        $socialMediaModels[] = new SocialMedia();
        if ($model->isNewRecord) {
            return $this->render('create', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
        } else {
            return $this->render('update', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
        }
    }

    /**
     * Creates a new Social Media Link.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionLink($id)
    {
        $model = $this->findModel($id);

        if ($model->user_id === Yii::$app->user->id) {
            return $this->redirect(['update', 'id' => $id]);
        }

        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();

        // make "linked" link for hashtags
        foreach ($socialMediaModels as $socialMediaModel) {
            if ($socialMediaModel->site_name === 'twitter') {
                $url_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
                if (!preg_match($url_regex, $socialMediaModel->url)) {
                    $socialMediaModel->url = preg_replace(
                        '/#?(\w+)/i',
                        'https://twitter.com/hashtag/$1?src=hash',
                        $socialMediaModel->url);
                }
            }
        }

        $linkModel = new SocialMedia();

        $postData = Yii::$app->request->post();

        if ($linkModel->load($postData) && $linkModel->validate()) {
            if ($linkModel->url === '') {
                $linkModel->delete();
            } else {
                $linkModel->event_id = $model->id;
                try {
                    $linkModel->save();
                } catch (IntegrityException $e) {
                    throw new \InvalidArgumentException('Url/Hashtag already exists');
                }
            }

            // delete cache of this event and create new cache
            Yii::$app->cache->delete('socialmedia' . $id);
            $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();
            $this->findSocialMedia($id, $socialMediaModels);

            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('link', ['model' => $model, 'linkModel' => $linkModel, 'socialMediaModels' => $socialMediaModels]);
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

    /**
     * Checks if socialmedia is in the cache, else loads social media content.
     */
    public function findSocialMedia($id, $socialMediaModels) {
        Yii::$app->cache->gc(true);
        if (!Yii::$app->cache->get('socialmedia' . $id)) {
            $socialMediaApi = new SocialMediaApi();
            foreach ($socialMediaModels as $key => $socialMediaModel) {
                $socialMediaApi->loadSocialMedia($socialMediaModels[$key]);
            }
            Yii::$app->cache->set('socialmedia' . $id, $socialMediaApi->getSocialMedia(), 300);
        }
    }
}
