<?php
use yii\helpers\Html;
?>
<p>You have entered the following information:</p>

<ul>
    <li><label>Name</label>: <?= Html::encode($model->name) ?></li>
    <li><label>Address</label>: <?= Html::encode($model->address) ?></li>
    <li><label>Description</label>: <?= Html::encode($model->description) ?></li>
</ul>