<?php

namespace app\controllers;

class GoabaseController extends \yii\web\Controller
{
    public function actionIndex($country="", $geoloc="", $limit=50)
    {
	$data['country'] = $country;
	$data['geoloc'] = $geoloc;
	$data['limit'] = $limit;
        return $this->render('index', $data);
    }

    public function actionView($id, $geoLat=0, $geoLon=0 ){
	if( isset($id) ){
		$data['id'] = $id;
		if($geoLat != 0 && $geoLon != 0){
			$data['geoLat'] = $geoLat;
			$data['geoLon'] = $geoLon;
		}
		return $this->render('view', $data);
	}
    }


}
