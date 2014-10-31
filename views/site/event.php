<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;

$this->title = $event->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-createEvent">
    <div class="body-content">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="row">
            <div class="col-md-8">
                <div class="col-md-12">
                    <div class="span12 map_searchEvent" id="map"></div>
                </div>
                <hr>

                <div class="col-md-12">
                </div>

            </div>

            <div class="col-md-4">

            </div>

        </div>
    </div>
</div>