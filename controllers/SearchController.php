<?php

namespace app\controllers;
use app\models\SearchEventForm;
use Yii;
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
	//$name, $adress, $from_date, $to_date, $type

	$searchModel = new SearchEventForm;
	if ( $searchModel->load(Yii::$app->request->post()) ){	

	  //print_r( $searchModel->type[1]['checked']);
	//print_r( $searchModel->type[1]);

	//print_r($searchModel);
	$results = $this->findEvent($searchModel->name, $searchModel->address);
	$results = $this->sortEventList($results);
	return $this->render('index', ['results' => $results, 'searchModel'=>$searchModel]);

	}

    }

//Anmerkung: GoaBase Parties haben keine Adresse, nur Koordinaten. deshalb können bis auf weiteres nur interne events nach adresse gesucht werden

    private function findEvent($searchName, $searchAdress){
	$results[] =  Array();
	$eventList = $this->createEventList();
	$eventList = $this->addGoaPartiesToEventList($eventList);

	$counter=0;
	for($i=0; $i < count ($eventList)-1; $i++){
		if ( strlen($searchName) > 0 && strpos(strtolower($eventList[$i]->name) , strtolower($searchName) ) != 0 ){ //search name
			$results[$counter] = new Event;
			$results[$counter] = $eventList[$i];
			$counter++;
		}else if( strlen($searchAdress) > 0 && strpos(strtolower($eventList[$i]->address) , strtolower($searchAdress) ) != 0 ){//search adress
			$results[$counter] = new Event;
			$results[$counter] = $eventList[$i];
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
