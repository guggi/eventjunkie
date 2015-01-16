<?php

use demogorgorn\ajax\AjaxSubmitButton;
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
                'id' => 'event-form',
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
                'convertFormat' => false,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:ii',
                    'todayHighlight' => true
                ]
            ]) ?>

            <?= $form->field($model, 'end_date')->widget(DateTimePicker::classname(),[
                'options' => ['placeholder' => date('d.m.Y G:i', time())],
                'convertFormat' => false, // für true php:d.m.Y G:i - konvertiert zu php Timestamp
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy hh:ii',
                    'todayHighlight' => true
                ],
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
                foreach ($socialMediaModels as $key=>$socialMediaModel) { ?>
                    <?php if ($key === 0) { ?>
                        <div id="<?= 'socialmedia-field' . $key ?>" class="field-visible">
                            <?= $form->field($socialMediaModel, '['.$key.']url')->textInput(['maxlength' => 500]) ?>
                        </div>
                    <?php } else { ?>
                        <div id="<?= 'socialmedia-field' . $key ?>" class="<?= ($key < $model->num_socialMedia) ? 'field-visible' : 'field-hidden' ?>">
                            <?= $form->field($socialMediaModel, '['.$key.']url')->textInput(['maxlength' => 500])->label('') ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            <div class="form-group">
                <button id=addField-button" class="btn btn-default" type="button" onClick="addField(this);">Add Field</button>
            </div>

            <?= $form->field($model, 'description')->textInput(['maxlength' => 1000])->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'note')->textInput(['maxlength' => 1000])->textarea(['rows' => 6]) ?>

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

<script>
    var numFields = JSON.parse('<?php echo json_encode($model->num_socialMedia) ?>');
    function addField(addFieldButton) {
        var maxNumFields = JSON.parse('<?php echo json_encode($model->max_num_socialMedia) ?>');
        numFields += 1;
        if (numFields <= maxNumFields) {
            var field = document.getElementById('socialmedia-field' + (numFields - 1));
            field.className = 'field-visible';
            if (numFields === maxNumFields) {
                addFieldButton.className = 'field-hidden';
            }
        }
    }
</script>


