<?php
use yii\helpers\Html;
?>
<p>You have entered the following information:</p>

<ul>
    <li><label>Name</label>: <?= Html::encode($model->name) ?></li>
    <li><label>Location</label>: <?= Html::encode($model->location) ?></li>
    <li><label>Description</label>: <?= Html::encode($model->description) ?></li>
    <li><label>Latitude</label>: <?= Html::encode($xlat) ?></li>
</ul>