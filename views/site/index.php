<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use kartik\widgets\RangeInput;
use kartik\widgets\DateTimePicker;
/* @var $this yii\web\View */
$this->title = 'EventJunkie';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-index">

    <div class="body-content">
        <h1><?= Html::encode($this->title) ?></h1>
        <div class="row">
            <div class="col-md-3">
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
                <?= $form->field($searchModel, 'distance')->widget(RangeInput::classname(), [
                    'value' => 1,
                    'options' => ['readonly' => false],
                    'html5Options' => ['min' => 0, 'max' => 50],
                    'addon' => ['append' => ['content' => 'km']]
                ]) ?>
                <?= $form->field($searchModel, 'from_date')->widget(DateTimePicker::classname(),[
                    'options' => ['placeholder' => date('d.m.Y G:i', time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'd.m.Y G:i',
                        'todayHighlight' => true
                    ]
                ]) ?>
                <?= $form->field($searchModel, 'to_date')->widget(DateTimePicker::classname(),[
                    'options' => ['placeholder' => date('d.m.Y G:i', time())],
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'format' => 'd.m.Y G:i',
                        'todayHighlight' => true
                    ]
                ]) ?>


                <div class="form-group">
                    <?= Html::submitButton('Search event', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
            <div class="col-md-7">
                <div class="col-md-12">
                    <div class="span12 map_searchEvent" id="map"></div>
                </div>
                <hr>


                <div class="col-md-12">

                    <?php $jsonMarkerList = []; ?>

                    <?php foreach ($eventList as $event): ?>
                        <?php $jsonMarkerList[] = [
                            "latitude" => $event->latitude,
                            "longitude" => $event->longitude] ?>

                        <div class="col-md-6">
                            <div class="list-group">
                                <a href="index.php?r=site/event&id=<?= $event->id ?>" class="list-group-item">
                                    <h4 class="list-group-item-heading"><?= Html::encode($event->name) ?></h4>
                                    <p class="list-group-item-text pull-left">
                                    </p>
                                    <p class="list-group-item-text">
                                        <?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
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
                </div>

                <div class="col-md-12">
                    <?= LinkPager::widget(['pagination' => $pagination]) ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="col-md-12">
                    <h4>Top events</h4>

                    <?php $i = 0; ?>

                    <?php foreach ($eventList as $event): ?>
                        <?php
                        if ($i++ == 3) {
                            break;
                        }
                        ?>

                        <div class="list-group">
                            <a href="#" class="list-group-item">
                                <h4 class="list-group-item-heading"><?= Html::encode($event->name) ?></h4>
                                <p class="list-group-item-text pull-left">
                                </p>
                                <p class="list-group-item-text">
                                    <?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
                                    <?= Html::encode($event->address) ?><br>
                                    added by Author <small>on Date</small>
                                </p>
                            </a>
                        </div>

                    <?php endforeach; ?>

                </div>
                <div class="col-md-12">
                    <h4>New events</h4>

                    <?php $i = 0; ?>

                    <?php foreach ($eventList as $event): ?>
                        <?php
                        if ($i++ == 3) {
                            break;
                        }
                        ?>

                        <div class="list-group">
                            <a href="#" class="list-group-item">
                                <h4 class="list-group-item-heading"><?= Html::encode($event->name) ?></h4>
                                <p class="list-group-item-text pull-left">
                                </p>
                                <p class="list-group-item-text">
                                    <?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
                                    <?= Html::encode($event->address) ?><br>
                                    added by Author <small>on Date</small>
                                </p>
                            </a>
                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
        </div>


    </div>
</div>
