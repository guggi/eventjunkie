<?php

namespace app\controllers;
use app\models\forms\EventSearchForm;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use app\models\Event;
use app\models\apis\GoaBaseApi;

class SearchController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSearch(){
        $searchModel = new EventSearchForm;

        if ($searchModel->load(Yii::$app->request->post()) && $searchModel->validate()){
            if (($searchModel->type_site == 1) && ($searchModel->type_goabase == 0)) {
                $query = $this->queryEvents($searchModel);
                $pagination = new Pagination([
                    'defaultPageSize' => 10,
                    'totalCount' => $query->count(),
                ]);

                // Event List ordered by date
                $eventList = $query->orderBy('start_date')
                    ->offset($pagination->offset)
                    ->limit(10)
                    ->all();

                // Daten für die Autovervollständiung
                foreach($eventList as $eventName)
                    array_push($searchModel->eventNameList, $eventName->name);

                // Top Events
                $topList = $query->orderBy(['clicks' => SORT_DESC])->limit(3)->all();

                // New Events
                $newList = $query->orderBy(['creation_date' => SORT_DESC])->limit(3)->all();

                return $this->render('@app/views/event/index', ['searchModel' => $searchModel,
                    'eventList' => $eventList,
                    'pagination' => $pagination, 'topList' => $topList, 'newList' => $newList]);
            } else {
                $results = $this->findEvent($searchModel->name, $searchModel->address);
                $results = $this->sortEventList($results);
                return $this->render('index', ['results' => $results, 'searchModel' => $searchModel]);
            }
        } else {
            return $this->redirect(['event/index']);
        }

    }

    private function queryEvents($searchModel) {
        $query = Event::find()->where(['like', 'name', $searchModel->name]);
        if (isset($searchModel->latitude)) {
            $query = $query->andWhere('acos(sin('.$searchModel->latitude . ') * sin(Latitude) + cos('.$searchModel->latitude . ') * cos(Latitude) * cos(Longitude - ('.$searchModel->longitude . '))) * 6371 <= ' .
                $searchModel->radius*1000);
        }
        if ($searchModel->from_date !== "") {
            $query = $query->andWhere(['>=', 'UNIX_TIMESTAMP(start_date)', strtotime($searchModel->from_date)]);
        } else {
            $query = $query->andWhere(['>=', 'UNIX_TIMESTAMP(end_date)', time()]);
        }
        if ($searchModel->to_date !== "") {
            $query = $query->andWhere(['<=', 'UNIX_TIMESTAMP(start_date)', strtotime($searchModel->to_date)]);
        }
        $query = $query->joinWith('user');

        return $query;
    }

    private function findEvent($searchName, $searchAdress){
        $results[] =  Array();
        $eventList = $this->createEventList();
        $eventList = $this->addGoaPartiesToEventList($eventList);

        $counter=0;
        for($i=0; $i < count ($eventList)-1; $i++){
            if ( strlen($searchName) > 0 && strpos(strtolower($eventList[$i]->name) , strtolower($searchName) ) > -1 ){ //search name
                $results[$counter] = new Event;
                $results[$counter] = $eventList[$i]; //add event to findList
                $counter++;
            }else if( strlen($searchAdress) > 0 && strpos(strtolower($eventList[$i]->address) , strtolower($searchAdress) ) > -1 ){//search adress
                $results[$counter] = new Event;
                $results[$counter] = $eventList[$i]; //add event to findList
                $counter++;
            }
        }
        return $results;
    }


    private function createEventList(){
        $query = Event::find()->where(['>=', 'UNIX_TIMESTAMP(start_date)', time()]);
        $query = $query->joinWith('user');
        $eventList = $query->orderBy('start_date')
            ->all();
        return $eventList;
    }

    private function addGoaPartiesToEventList($eventList){

        $goa = new GoaBaseApi();
        $goaId = count($eventList);

        Yii::$app->cache->gc(true);

        if (Yii::$app->cache->get('goabase') == NULL) { //if not in cache load from api
            //list of goa parties, Type: ArrayList with Events
            $goaParties = $goa->getParties();
            Yii::$app->cache->set('goabase', $goaParties, 300);
        } else { //load from cache
            $goaParties = Yii::$app->cache->get('goabase');
        }

        for ($i=0 ; $i < count($goaParties) ; $i++) {
            $eventList[$goaId] = $goaParties[$i];
            ++$goaId;
        }
        return $eventList;
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
