<?php
//if( isset($party) )print_r($party);

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
	     <!-----Goolge-Maps----->
		  <div class="span12 map_searchEvent" id="map"></div>
		   <hr>
	<?php echo "<b>Info: </b>".$party['party']['textMore']; ?>
	<br>
	<?php echo "<b>Line-up: </b>".$party['party']['textLineUp']; ?>
	<br>
	<?php echo "<b>Last modified: </b>".$party['party']['dateModified']; ?>
	
	</div>
