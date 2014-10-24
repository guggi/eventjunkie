<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Create event';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-createEvent">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'name') ?>

            <?= $form->field($model, 'address') ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-8">
            <div class="span12" id="map"></div>
        </div>
    </div>
</div>