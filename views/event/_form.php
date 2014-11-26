<?php

use yii\helpers\Html;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $jsonMarkerList[0] = [
    "id" => $model->id,
    "name" => $model->name,
    "start_date" => $model->start_date,
    "end_date" => $model->end_date,
    "address" => $model->address,
    "latitude" => $model->latitude,
    "longitude" => $model->longitude
] ?>

<div class="event-form">

    <div class="row">
        <div class="col-md-4">
            <?php if (Yii::$app->session->hasFlash('error')){ ?>
                <div class="alert alert-danger">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php } ?>

            <?php $form = ActiveForm::begin([
                'options' => ['enctype'=>'multipart/form-data'],
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
            <?= $form->field($model, 'address')->textInput([
                'onchange' => 'codeAddress()',
                'onkeyup' => 'codeAddress()',
            ])?>
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

            <?php if ($model->image) { ?>
                <?= $form->field($model, 'upload_image')->widget(FileInput::className(), [
                    'pluginOptions' => [
                        'showUpload' => false,
                        'showRemove' => false,
                        'initialPreview'=>[
                            Html::img('images/' . $model->image, ['class'=>'file-preview-image']),
                        ],
                        'initialCaption' => $model->image,
                    ],
                ])
                ?>
            <?php } else { ?>
                <?= $form->field($model, 'upload_image')->widget(FileInput::className(), [
                    'pluginOptions' => [
                        'showUpload' => false,
                        'showRemove' => false,
                    ],
                ])
                ?>
            <?php } ?>

            <?php if (isset($socialMediaModels)) {
                foreach ($socialMediaModels as $key => $value) { ?>
                    <?php if ($key === 0) { ?>
                        <?= $form->field($socialMediaModels[$key], 'url')->textInput(['maxlength' => 500]) ?>
                    <?php } else { ?>
                        <?= $form->field($socialMediaModels[$key], 'url')->textInput(['maxlength' => 500])->label('') ?>
                    <?php } ?>
                    <?= $key ?>
                <?php } ?>
            <?php } ?>

            <?= $form->field($model, 'description')->textInput(['maxlength' => 1000])->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-8">
            <div class="span12 map_createEvent" id="map"></div>
        </div>
    </div>

</div>

<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
    var streetZoom = 15;
</script>