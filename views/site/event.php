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

    </div>
</div>