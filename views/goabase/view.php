<?php
use app\components\GoaBaseWidget;

$this->title = 'EventJunkie - Goa party';
?>


<?= GoaBaseWidget::widget(['id' => $id]) ?>


<!--location for google maps -->
<?php $jsonMarkerList[] = ["latitude" => $geoLat, "longitude" => $geoLon]; ?>
<!--------------------->
<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
</script>

