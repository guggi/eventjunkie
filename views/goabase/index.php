<?php
/* @var $this yii\web\View */
use app\components\GoaBaseWidget;
$this->title = 'EventJunkie - Goabase';
?>
<h1>Goabase</h1>

<p>

<?php
echo $country ." , ".$geoloc;

?>

<br>

<?= GoaBaseWidget::widget(['country' => $country, 'geoloc'=>$geoloc, 'limit' => $limit] ) ?>
</p>
