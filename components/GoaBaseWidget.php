<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class GoaBaseWidget extends Widget{

    private $path = "https://www.goabase.net/party/api/json/";  //500 most newest parties; default without parameters
    //$_GET parameter:
    public $country; //parties just in a specific country
    public $geoloc; //parties just in a specific location (for instance a city)
    public $ll; //parties 120km near by the specified Latitude and Longitude
    public $limit=50; //default the newest 500 most newest parties; limit changes the default value of 500
    public $id;

    public function init(){
        parent::init();
	$firstParam = true;
	//add country to the $_GET parameters
        if ($this->country !== null) {
	    if($firstParam){ $this->path = $this->path."?country=".$this->country; $firstParam = false;}
            else $this->path = $this->path."&country=".$this->country;
        } 

	//add geo location to the $_GET parameters
        if ($this->geoloc !== null) {
	    if($firstParam){ $this->path = $this->path."&geoloc=".$this->geoloc; $firstParam = false;}
            else $this->path = $this->path."&geoloc=".$this->geoloc;
        } 

	//add parameter near by 120km to the $_GET parameters
	if ($this->ll !== null) {
	    if($firstParam){ $this->path = $this->path."&ll=".$this->ll; $firstParam = false; }
            else $this->path = $this->path."&ll=".$this->ll;
        } 

	//add party number limit to the $_GET parameters
	if ($this->limit !== null) {
	    if($firstParam){ $this->path = $this->path."&limit=".$this->limit; $firstParam = false;}
            else $this->path = $this->path."&limit=".$this->limit;
        } 

/*	$session = \Yii::$app->session;
	// open a session
	$session->open();

	if(  isset($session['goaParties'])==false ){
		//$session['goaParties'] = $goaParties;
/*	}else{//if session var is already set
		$goaParties = $session['goaParties'];
	} */

	//if id is not set display whole party list
	if( !isset($this->id) ){
		// Read the json file contents into a string variable,
		//$str_data = file_get_contents("https://www.goabase.net/party/api/json/");
		$str_data = file_get_contents($this->path);

		//and parse the string into a data structure
		$goaParties = json_decode($str_data,true);

		$this->display($goaParties);

	}
	else{//display party with specified id
		// Read the json file contents into a string variable,
		$str_data = file_get_contents("https://www.goabase.net/party/api/json/".$this->id);
		//and parse the string into a data structure
		$party = json_decode($str_data,true);
		$this->displayParty($party);
	}

    }

    public function run(){
    }

    //Structure of PartyList: 
    //nameParty: $partyList['partylist']['index']['nameParty'];  	//Party name
    //dateStart: $partyList['partylist']['index']['dateStart'];   	//Start date
    //dateEnd: $partyList['partylist']['index']['dateEnd'];		//End date
    //nameType: $partyList['partylist']['index']['nameType'];		//Type of pary (indoor/outdoor)
    //nameCountry: $partyList['partylist']['index']['nameCountry'];	//Country
    //nameTown: $partyList['partylist']['index']['nameTown'];		//Town name
    //geoLat: $partyList['partylist']['index']['geoLat'];		//Latitude
    //geoLon: $partyList['partylist']['index']['geoLon'];		//Longitude
    //nameOrganizer: $partyList['partylist']['index']['nameOrganizer'];	//Organizer name

    //urlOrganizer: $partyList['partylist']['index']['urlOrganizer'];	//WebSite of organizer
    //urlImageSmall: $partyList['partylist']['index']['urlImageSmall'];	//Url to pary image
    //dateCreated: $partyList['partylist']['index']['dateCreated'];	//Date of creation
    //dateModified: $partyList['partylist']['index']['dateModified'];	//Last modified date
    //nameStatus: $partyList['partylist']['index']['nameStatus'];	//Party status

    //display party list
    public function display($partyList){

	echo "<div class='col-md-12'>";
	for($i=0; $i < count($partyList['partylist']); $i++){

                 echo "<div class='col-md-6' style='width:200px;height:320px;overflow-y:auto;'>";
                       echo "<div class='list-group' style='height:250px;'>";
		           echo "<a href=".\Yii::$app->request->BaseUrl."/index.php?r=goabase/view&id=".$partyList['partylist'][$i]['id']."&geoLat=".$partyList['partylist'][$i]['geoLat']."&geoLon=".$partyList['partylist'][$i]['geoLon'].">";
                             echo "<h4 class='list-group-item-heading'>".$partyList['partylist'][$i]['nameParty']."</h4>";
			   echo "</a>";
                               echo "<p class='list-group-item-text pull-left'>";
                               echo "</p>";
                               echo "<p class='list-group-item-text'>";

        echo "<a href=".\Yii::$app->request->BaseUrl."/index.php?r=goabase/view&id=".$partyList['partylist'][$i]['id']."&geoLat=".$partyList['partylist'][$i]['geoLat']."&geoLon=".$partyList['partylist'][$i]['geoLon'].">";
		if($partyList['partylist'][$i]['urlImageSmall']!=null){
			echo "<img src=".$partyList['partylist'][$i]['urlImageSmall']."  style='max-heigth:90px;'/><br><br>"; 
		}
		//else echo "<br><img src=".\Yii::$app->request->BaseUrl."/goaDefault.JPG"."  style='max-heigth:100px;'/><br><br>"; 
	echo "</a>";
				  
				    $startTime = explode('T', $partyList['partylist'][$i]['dateStart']);
				if( count($startTime)>1 ) echo "<b>Start: </b>".$startTime[0].", ".substr($startTime[1], 0, strlen($startTime[1]) - 6) ; 
				    echo "<br>";
				    echo $partyList['partylist'][$i]['nameCountry']." - ";
				    echo $partyList['partylist'][$i]['nameTown'];	
				    echo "<br>";
                                    echo "<small>Organizer: ".$partyList['partylist'][$i]['nameOrganizer']."</small>";

                              echo "</p>";
                        echo "</div>";
                    echo "</div>";
	}
	echo "</div>";

    }

    //display single party
    public function displayParty($party){
	echo "<h2 style='position:relative;left:2%;'>".$party['party']['nameParty']."</h2>";
	echo "<hr>";
	?>
	<!-- Google maps ------>
	
	<div class="col-md-7" >

	<?php 
	//image big
	if( isset($party['party']['urlImageFull']) ){
		echo "<img src=".$party['party']['urlImageFull']." style='max-height:600px;max-width:600px;'/><br>"; 
	}
	?>
	<br>

	<?php
	//party start
	$startTime = explode('T', $party['party']['dateStart']);
	if( count($startTime)>1 ) echo "<b>Start: </b>".$startTime[0].", ".substr($startTime[1], 0, strlen($startTime[1]) - 6) ; 
	?>
	<br>
	<?php
	$endTime = explode('T', $party['party']['dateEnd']);
	if( count($endTime)>1 ) echo "<b>End: </b>".$endTime[0].", ".substr($endTime[1], 0, strlen($endTime[1]) - 6) ; 
	?>
	<br>
	<?php echo "<b>Type: </b>".$party['party']['nameType']; ?>
	<br>
	<?php echo "<b>Entry fee:</b> ".$party['party']['textEntryFee']; ?>
	<br>
	<?php echo "<b>Status: </b>".$party['party']['nameStatus']; ?>
	<br>
	<?php echo "<b>Organizer: </b>".$party['party']['nameOrganizer']; ?>
	<br>
	<?php echo "<b>Url: </b><a href=".$party['party']['urlParty'].">".$party['party']['urlParty']."</a>"; ?>
	<br>
	<?php echo "<b>Location:</b> ".$party['party']['textLocation']; ?>
	<br>
	<?php echo $party['party']['nameCountry']." - ".$party['party']['nameTown']; ?>
	<br>
	<br>
	     <!-----Google-Maps----->
		  <div class="span12 map_searchEvent" id="map"></div>
		   <hr>
	<br>
	<?php echo "<b>Info: </b>".$party['party']['textMore']; ?>
	<br>
	<?php echo "<b>Line-up: </b>".$party['party']['textLineUp']; ?>
	<br>
	<?php echo "<b>Last modified: </b>".$party['party']['dateModified']; ?>
	
	</div>

	<?php
    }


}

?>


