<?php

namespace app\models\apis;

use app\models\Event;
use Exception;
use Yii;

class GoaBaseApi {

    private $url= "https://www.goabase.net/party/api/json/?country=austria";
    //single party infos example: https://www.goabase.net/party/api/json/81655

    function __construct() {
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
            $goaObject[$i]->user_id = "Goabase: ".$partyList['partylist'][$i]['nameOrganizer'];;//"Goabase: ".$partyList['partylist'][$i]['nameOrganizer'];

            $goaObject[$i]->creation_date  = $partyList['partylist'][$i]['dateCreated'];

            $goaObject[$i]->start_date  = $this->dateFormat($partyList['partylist'][$i]['dateStart']);
            $goaObject[$i]->end_date  = $this->dateFormat($partyList['partylist'][$i]['dateEnd']);


            $goaObject[$i]->latitude  = $partyList['partylist'][$i]['geoLat'];
            $goaObject[$i]->longitude  = $partyList['partylist'][$i]['geoLon'];
	
	    $goaObject[$i]->address = $this->getAdressFromLatLng( $goaObject[$i]->latitude,  $goaObject[$i]->longitude);

            $goaObject[$i]->image  = $partyList['partylist'][$i]['urlImageSmall'];

            //$goaObject[$i]->clicks  = $partyList['partylist'][$i]['dateCreated'];
            //$goaObject[$i]->description  = $partyList['partylist'][$i]['dateCreated'];
            //$goaObject[$i]->note  = $partyList['partylist'][$i]['dateCreated'];
        }
        return $goaObject;
    }

    function findEvent($searchName, $searchAdress){
        $results[] =  Array();
        $eventList = $this->getParties();

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

//-----------------------Private-------------------------------

    //Change date Format from 2014-12-11T22:00:00+01:00 to 2014-12-13 22:30:00
    private function dateFormat($goaBaseDate){
	    //Date format from the goabase api: 2014-12-11T22:00:00+01:00"
	    $goaBaseDate = substr($goaBaseDate, 0, 19); // format example: 2014-12-13T22:30:00
	    $goaBaseDate = str_replace('T', ' ', $goaBaseDate); //replace T with whitespace
	    return $goaBaseDate;
    }


    //example for google api: https://maps.googleapis.com/maps/api/geocode/json?latlng=40.714224,-73.961452&sensor=true
    private function getAdressFromLatLng($lantitude, $longitude){

        $jsonData = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' .
                $lantitude . ','.$longitude.'&sensor=true');

 	$data = json_decode($jsonData);

       if ($data->{'status'} != 'OK') {
        	return;
        }else{
		return $data->{'results'}[0]->{'formatted_address'};
	} 

    }


} 
