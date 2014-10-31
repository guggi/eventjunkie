<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;

$this->title = 'Create event';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-createEvent">
    <div class="body-content">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="row">
            <div class="col-md-4">
                <?php if (Yii::$app->session->hasFlash('error')){ ?>
                    <div class="alert alert-danger">
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php } ?>

                <?php $form = ActiveForm::begin([
                    'layout' => 'default',
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-4',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-8',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                ]); ?>

                <?= $form->field($model, 'name') ?>
                <?= $form->field($model, 'address') ?>
                <?= $form->field($model, 'start_date')->widget(DateTimePicker::classname(),[
                    'options' => ['placeholder' => date('d.m.Y G:i', time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'd.m.Y G:i',
                        'todayHighlight' => true
                    ]
                ]) ?>

                <?= $form->field($model, 'end_date')->widget(DateTimePicker::classname(),[
                    'options' => ['placeholder' => date('d.m.Y G:i', time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'd.m.Y G:i',
                        'todayHighlight' => true
                    ]
                ]) ?>

                <?= $form->field($model, 'image')->widget(FileInput::className(), [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'showUpload' => false
                    ]
                ])
                ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Create event', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>

            <div class="col-md-8">
                <div class="span12 map_createEvent" id="map"></div>
            </div>
        </div>
    </div>
</div>