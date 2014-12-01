<?php

use yii\helpers\Html;
use kartik\widgets\DateTimePicker;
use kartik\widgets\FileInput;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Link Social Media with Event: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Link with Social Media';
?>

<div class="event-link">

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

            <!-- Only show if event is linked with either facebook, flickr or twitter -->
            <?php if ($socialMediaModels) { ?>

                <p class="tiny text-center">Linked with:
                    <?php foreach ($socialMediaModels as $socialMediaModel) {?>
                        <?php if ($socialMediaModel->site_name === 'facebook') {?>
                            <a class="btn btn-social-icon btn-facebook">
                                <i class="fa fa-facebook"></i>
                            </a>
                        <?php } ?>
                        <?php if ($socialMediaModel->site_name === 'flickr') {?>
                            <a class="btn btn-social-icon btn-flickr" href="<?= $socialMediaModel->url ?>">
                                <i class="fa fa-flickr"></i>
                            </a>
                        <?php } ?>
                        <?php if ($socialMediaModel->site_name === 'twitter') {?>
                            <a class="btn btn-social-icon btn-twitter" href="<?= $socialMediaModel->url ?>">
                                <i class="fa fa-twitter"></i>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </p>

            <?php }?>

            <?= $form->field($linkModel, 'url')->textInput(['maxlength' => 500]) ?>

            <div class="form-group">
                <?= Html::submitButton('Link with Social Media', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>


