<?php

namespace app\models\apis;

use app\models\Event;
use Exception;
use Yii;

class GoaBaseApi {

    private $url= "https://www.goabase.net/party/api/json/?country=austria";
    //single party infos example: https://www.goabase.net/party/api/json/81655

    function __construct() {
        //   $this->api_key = Yii::$app->params['flickrApiKey'];
        //$this->name = $name;
    }


    //returns a list with event objects of goaparties
    //limit is number of parties in the goaObject list
    function getParties($limit=10){
        // Read the json file contents into a string variable,
        $str_data = file_get_contents($this->url);

        //and parse the string into a data structure
        $partyList = json_decode($str_data,true);

        $goaObject[$limit] = Array();

        for($i=0;$i<$limit; $i++){
            $goaObject[$i] = new Event();

            $goaObject[$i]->name  = $partyList['partylist'][$i]['nameParty'];
            $goaObject[$i]->id = "goabase".$partyList['partylist'][$i]['id'];
            $goaObject[$i]->user_id = "goabase".$partyList['partylist'][$i]['nameOrganizer'];;//"Goabase: ".$partyList['partylist'][$i]['nameOrganizer'];

            $goaObject[$i]->creation_date  = $partyList['partylist'][$i]['dateCreated'];
            $goaObject[$i]->start_date  = $partyList['partylist'][$i]['dateStart'];
            $goaObject[$i]->end_date  = $partyList['partylist'][$i]['dateEnd'];

            $goaObject[$i]->address  = $partyList['partylist'][$i]['textLocation']; // ?
            $goaObject[$i]->latitude  = $partyList['partylist'][$i]['geoLat'];
            $goaObject[$i]->longitude  = $partyList['partylist'][$i]['geoLon'];

            $goaObject[$i]->image  = $partyList['partylist'][$i]['urlImageSmall'];

            //$goaObject[$i]->clicks  = $partyList['partylist'][$i]['dateCreated'];
            //$goaObject[$i]->description  = $partyList['partylist'][$i]['dateCreated'];
            //$goaObject[$i]->note  = $partyList['partylist'][$i]['dateCreated'];
        }
        return $goaObject;
    }


    //get single party
    //id example: goabase1234
    function getParty($id){
        $id =  substr($id, 7, strlen($id)); //cut "goabase" part away
        // Read the json file contents into a string variable,
        $str_data = file_get_contents("https://www.goabase.net/party/api/json/".$id);
        //and parse the string into a data structure
        $party = json_decode($str_data,true);
        return $party;
    }

} 
