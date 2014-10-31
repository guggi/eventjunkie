<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'creation_date')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'latitude')->textInput(['maxlength' => 17]) ?>

    <?= $form->field($model, 'longitude')->textInput(['maxlength' => 17]) ?>

    <?= $form->field($model, 'start_date')->textInput() ?>

    <?= $form->field($model, 'end_date')->textInput() ?>

    <?= $form->field($model, 'image')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'facebook')->textInput(['maxlength' => 1000]) ?>

    <?= $form->field($model, 'twitter')->textInput(['maxlength' => 1000]) ?>

    <?= $form->field($model, 'goabase')->textInput(['maxlength' => 1000]) ?>

    <?= $form->field($model, 'flickr')->textInput(['maxlength' => 1000]) ?>

    <?= $form->field($model, 'clicks')->textInput() ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => 1000]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
