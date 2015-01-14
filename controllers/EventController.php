<?php

namespace app\controllers;

use app\models\EventSearch;
use app\models\apis\SocialMediaApi;
use app\models\apis\GoaBaseApi;

use app\models\SocialMedia;
use Yii;
use app\models\Event;
use yii\base\Exception;
use yii\helpers\Json;
use yii\base\Model;
use yii\db\IntegrityException;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use yii\data\Pagination;
use app\models\forms\EventSearchForm;

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
                'only' => ['create', 'update', 'delete', 'list', 'admin'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete', 'list', 'admin'],
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
        Yii::$app->cache->gc(true);
        $searchModel = new EventSearchForm();

        $query = Event::find()->where(['>=', 'UNIX_TIMESTAMP(end_date)', time()]);

        $query = $query->joinWith('user');

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'totalCount' => $query->count(),
        ]);

        $goa = new GoaBaseApi();

        if (Yii::$app->cache->get('goabase') == NULL) { //if not in cache load from api
            //list of goa parties, Type: ArrayList with Events
            $goaParties = $goa->getParties();
            Yii::$app->cache->set('goabase', $goaParties, 300);
        } else { //load from cache
            $goaParties = Yii::$app->cache->get('goabase');
        }
        // Event List ordered by date
        $eventList = $query->orderBy('start_date')->all();

        // Daten für die Autovervollständiung
        foreach($eventList as $eventName)
            array_push($searchModel->eventNameList, $eventName->name);

        //-----Add GoaParties to eventList--------
        $goaId = $query->count();

        //append goaparties to eventList (intern parties)
        for ($i=0 ; $i < count($goaParties) ; $i++) {
            $eventList[$goaId] = $goaParties[$i];
            ++$goaId;
        }

        // Sort Event List
        $eventList = $this->sortEventList($eventList);

        // Top Events
        $topList = $query->orderBy(['clicks' => SORT_DESC])->limit(3)->all();

        // New Events
        $newList = $query->orderBy(['creation_date' => SORT_DESC])->limit(3)->all();

        return $this->render('index', ['searchModel' => $searchModel,
            'eventList' => $eventList,
            'pagination' => $pagination, 'topList' => $topList, 'newList' => $newList]);
    }

    public function actionAsyncloading($search = null, $id = null) {
        $out = ['more' => false];
        
        if (!is_null($search)) {
            $query = new Query;
            $query->select('id, name AS text')
                ->from('event')
                ->where('name LIKE "%' . $search .'%"')
                ->limit(20);
            $command = $query->createCommand();
            $data = $command->queryAll();
            $out['results'] = array_values($data);
        
        } else if ($id > 0) {
            $out['results'] = ['id' => $id, 'text' => Event::find($id)->name];
        
        } else {
            $out['results'] = ['id' => 0, 'text' => 'No matching records found'];
        }
        echo Json::encode($out);
    }


    /**
     * Load Goa party from goabase api
     */
    public function actionLoadgoaparty($id){
        $goaBaseApi = new GoaBaseApi();
        $goaParty = $goaBaseApi->getParty($id);
        return $this->render('goaparty', ['party' => $goaParty]);
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

        ++$model->clicks;

        $model->update();

        // make "linked" link for hashtags
        foreach ($socialMediaModels as $socialMediaModel) {
            if ($socialMediaModel->site_name == 'twitter') {
                $url_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
                $user_regex = '/(\@)[a-zA-Z0-9\_\-]*/';
                if (!preg_match($url_regex, $socialMediaModel->url)) {
                    if (preg_match($user_regex, $socialMediaModel->url)) {
                        $socialMediaModel->url = preg_replace(
                        '/\@(\w+)/i',
                            'https://twitter.com/$1',
                            $socialMediaModel->url);
                    } else {
                        $socialMediaModel->url = preg_replace(
                            '/#?(\w+)/i',
                            'https://twitter.com/hashtag/$1?src=hash',
                            $socialMediaModel->url);
                    }
                }
            }
        }

        return $this->render('view', [
            'model' => $model, 'socialmedia' => Yii::$app->cache->get('socialmedia' . $id),
            'socialMediaModels' => $socialMediaModels,
        ]);
    }

    public function actionGallery($id) {
        $model = $this->findModel($id);
        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();

        $this->findSocialMedia($id, $socialMediaModels);

        return $this->render('gallery', [
            'model' => $model, 'socialmedia' => Yii::$app->cache->get('socialmedia' . $id),
        ]);
    }

    /**
     * Creates a new Event model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @throws ForbiddenHttpException
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        $model = new Event();

        $socialMediaModels = [];

        $model->max_num_socialMedia = 20;
        $model->num_socialMedia = 1;

        for ($i = 0; $i < $model->max_num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();

        if ($model->load($postData) && $model->validate() && Model::loadMultiple($socialMediaModels, $postData) && Model::validateMultiple($socialMediaModels)) {
            $model->user_id = Yii::$app->user->id;

            $model->clicks = 0;

            // image
            $image = $model->uploadImage();
            if ($model->save()) {
                if ($image != null) {
                    $image->saveAs($model->getImagePath());
                }

                foreach ($socialMediaModels as $socialMediaModel) {
                    if ($socialMediaModel->url == '') {
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
        } else {
            if (strtotime($model->start_date) != 0) {
                $model->start_date = date('d.m.Y G:i', strtotime($model->start_date));
            }
            if (strtotime($model->end_date) != 0) {
                $model->end_date = date('d.m.Y G:i', strtotime($model->end_date));
            }
        }
        return $this->render('create', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
    }

    /**
     * Updates an existing Event model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->can('admin') || (Yii::$app->user->id != $model->user_id)) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        /*if ($model->user_id !== Yii::$app->user->id) {
            return $this->redirect(['view', 'id' => $id]);
        }*/

        $query = SocialMedia::find()->where(['event_id' => $id])->orderBy('id');
        $socialMediaModels = $query->all();

        $model->max_num_socialMedia = 20;
        $model->num_socialMedia = intval($query->count());

        for ($i = 0; $i < $model->max_num_socialMedia - $model->num_socialMedia; $i++) {
            $socialMediaModels[] = new SocialMedia();
        }

        $postData = Yii::$app->request->post();

        $old_image = $model->image;

        if ($model->load($postData) && $model->validate() && Model::loadMultiple($socialMediaModels, $postData) && Model::validateMultiple($socialMediaModels)) {
            // image
            $image = $model->uploadImage();

            if ($image == null) {
                $model->image = $old_image;
            }

            if ($model->update() != false) {
                if ($image != null) { // delete old and overwrite
                    $model->deleteImage($old_image);
                    $image->saveAs($model->getImagePath());
                }

                foreach ($socialMediaModels as $socialMediaModel) {
                    if ($socialMediaModel->url == '') {
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
            if (strtotime($model->start_date) != 0) {
                $model->start_date = date('d.m.Y G:i', strtotime($model->start_date));
            }
            if (strtotime($model->end_date) != 0) {
                $model->end_date = date('d.m.Y G:i', strtotime($model->end_date));
            }
        }
        return $this->render('update', ['model' => $model, 'socialMediaModels' => $socialMediaModels]);
    }

    /**
     * Creates a new Social Media Link.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionLink($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->id == $model->user_id) {
            return $this->redirect(['update', 'id' => $id]);
        }

        $socialMediaModels = SocialMedia::find()->where(['event_id' => $id])->orderBy('id')->all();

        // make "linked" link for hashtags
        foreach ($socialMediaModels as $socialMediaModel) {
            if ($socialMediaModel->site_name == 'twitter') {
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
            if ($linkModel->url == '') {
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
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!Yii::$app->user->can('admin') && Yii::$app->user->id != $model->user_id) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        $model->deleteImage($model->image);
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * @throws ForbiddenHttpException
     * @return string
     */
    public function actionList() {
        if (Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        $searchModel = new EventSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), Yii::$app->user->id);
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * @throws ForbiddenHttpException
     * @return string
     */
    public function actionAdmin() {
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams(), '');
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Is called by the cron job, checks if a cronjob is running and reloads all social media content.
     */
    public function actionCron() {
        if (!Yii::$app->cache->get('cron')) {
            Yii::$app->cache->set('cron', true);
            try {
                $eventModels = Event::find()->all();
                foreach ($eventModels as $eventModel) {
                    $socialMediaModels = SocialMedia::find()->where(['event_id' => $eventModel->id])->orderBy('id')->all();
                    Yii::$app->cache->delete('socialmedia' . $eventModel->id);
                    $this->findSocialMedia($eventModel->id, $socialMediaModels);
                }
            } catch (Exception $e) {
                Yii::$app->cache->delete('cron');
                return $e;
            }
            Yii::$app->cache->delete('cron');
            return 'Cron-job successful.';
        } else {
            return 'Cron-job already running.';
        }
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
        if (($model = Event::findOne($id)) != null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Checks if socialmedia is in the cache, else loads social media content.
     */
    public function findSocialMedia($id, $socialMediaModels) { //todo als Cron-Job oder ähnliches
        //Yii::$app->cache->gc(true);
        if (!Yii::$app->cache->get('socialmedia' . $id)) {
            $socialMediaApi = new SocialMediaApi();
            foreach ($socialMediaModels as $key => $socialMediaModel) {
                $socialMediaApi->loadSocialMedia($socialMediaModels[$key]);
            }
            Yii::$app->cache->set('socialmedia' . $id, $socialMediaApi->getSocialMedia());
        }
    }

    //Sort date from eventList 
    private function sortEventList($eventList){
        $anz = count($eventList);
        $temp="";
        for ($a = 0; $a < $anz; $a++) {
            for ($b = 0; $b < $anz -2; $b++) {
                if ($eventList[$b +1]->start_date < $eventList[$b]->start_date) {
                    $temp = $eventList[$b];
                    $eventList[$b] = $eventList[$b +1];
                    $eventList[$b +1] = $temp;
                }
            }
        }
        return $eventList;
    }

}
