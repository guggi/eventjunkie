<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
/* @var $this yii\web\View */
$this->title = 'EventJunkie';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-md-2">
                <h4>Search event</h4>
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

                <?= $form->field($searchModel, 'name') ?>
                <?= $form->field($searchModel, 'address') ?>

                <div class="form-group">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
            <div class="col-md-8">
                <div class="col-md-12">
                    <div class="span12" id="map"></div>
                </div>
                <hr>

                <?php $jsonMarkerList = []; ?>

                <?php foreach ($eventList as $event): ?>
                    <?php $jsonMarkerList[] = ["latitude" => $event->latitude,
                                        "longitude" => $event->longitude] ?>

                    <div class="col-md-6">
                        <div class="list-group">
                            <a href="#" class="list-group-item">
                                <h4 class="list-group-item-heading"><?= Html::encode($event->name) ?></h4>
                                <p class="list-group-item-text pull-left">
                                </p>
                                <p class="list-group-item-text">
                                    <?= Html::encode($event->eventDate) ?><br>
                                    <?= Html::encode($event->address) ?><br>
                                    added by Author <small>on Date</small>
                                </p>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <script>
                    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
                </script>
<!--
                <div class="col-md-6">
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <h4 class="list-group-item-heading">List group item heading</h4>
                            <p class="list-group-item-text pull-left"> ewrewer
                            </p>
                            <p class="list-group-item-text">
                                Date<br>
                                Location<br>
                                added by Author <small>on Date</small>
                            </p>
                        </a>
                    </div>
                </div>-->

                <div class="col-md-12">
                    <?= LinkPager::widget(['pagination' => $pagination]) ?>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>

    </div>
</div>
