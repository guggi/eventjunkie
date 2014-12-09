<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\LinkPager;
use kartik\widgets\RangeInput;
use kartik\widgets\DateTimePicker;
/* @var $this yii\web\View */
$this->title = 'EventJunkie - Event wall';
$this->params['breadcrumbs'][] = $this->title;
?>


<script>

    function advancedSearch(searchOptionButton){
        var searchForm = document.getElementById('searchForm');

        if(searchForm.className == 'field-hidden'){
            searchForm.className = 'field-visible';
            searchOptionButton.innerHTML = 'Simple Search';
        } else {
            searchForm.className = 'field-hidden';
            searchOptionButton.innerHTML = 'Advanced Search';
        }
    }

</script>


<div class="site-index">

    <div class="body-content">
        <!--   <h1><?= Html::encode($this->title) ?></h1> -->
        <br>
        <div class="row">
            <div class="col-md-3">
                <h4>Search event</h4>
                <?php $form = ActiveForm::begin([
                    'layout' => 'default',
                    'method' => 'get',
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
                <div id="searchForm" class="field-hidden">
                    <?= $form->field($searchModel, 'radius')->widget(RangeInput::classname(), [
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


                    <?= $form->field($searchModel, 'type')->checkboxList([
                            0 => 'Site',
                            1 => 'Goabase']
                    )?>

                </div>
                <div class="form-group">
                    <button id="searchOptionButton" class="btn btn-default" type="button" onClick="advancedSearch(this);">Advanced Search</button>
                </div>
                <div class="form-group">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>

            <div class="col-md-7">
                <!-- Google maps ------>
                <div class="span12 map_searchEvent" id="map"></div>
                <?php $jsonMarkerList = []; ?>
                <!--------------------->

                <hr>

                <?php foreach ($eventList as $event): ?>
                    <!-- Marker for Google Map -->
                    <?php
                    try { //ohne try catch block -> trying to get property of non-object
                        $jsonMarkerList[] = [
                            "id" => $event->id,
                            "name" => $event->name,
                            "start_date" => $event->start_date,
                            "end_date" => $event->end_date,
                            "address" => $event->address,
                            "latitude" => $event->latitude,
                            "longitude" => $event->longitude
                        ] ?>
                        <div class="col-md-6">
                            <div class="list-group">
                                <a href="<?= (substr($event->id, 0, 7) === "goabase") ? \Yii::$app->request->getBaseUrl().'/index.php?r=event/loadgoaparty&id='.$event->id : \Yii::$app->request->getBaseUrl().'/index.php?r=event/view&id='.$event->id ?>" class="list-group-item">
                                    <h3 class="list-group-item-heading"><?= Html::encode($event->name) ?></h3>
                                    <p class="list-group-item-text pull-left">
                                    </p>
                                    <?php if ($event->image) { ?>
                                        <div class="thumbnail-clipwrapper">
                                            <div class="thumbnail-clip">
                                                <img src="<?= (substr($event->id, 0, 7) === "goabase") ? Html::encode($event->image) : "images/" . Html::encode($event->image) ?>"><br>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <p class="list-group-item-text">
                                        <strong>Start: </strong><?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
                                        <?= Html::encode($event->address) ?><br>
                                        <small><?= (substr($event->id, 0, 7) === "goabase") ? Html::encode($event->user_id) : Html::encode($event->user["username"]) ?> (<?= Html::encode(date("d.m.Y G:i", strtotime($event->creation_date))) ?>)</small>
                                    </p>
                                </a>
                            </div>
                        </div>
                    <?php

                    }catch(Exception $e){}
                endforeach; ?>


                <?= LinkPager::widget(['pagination' => $pagination]) ?>
            </div>
            <div class="col-md-2">
                <h4>Top events</h4>

                <?php foreach ($topList as $event): ?>

                    <div class="list-group">
                        <a href="<?= \Yii::$app->request->BaseUrl.'/index.php?r=event/view&id='.$event->id ?>" class="list-group-item">
                            <h3 class="list-group-item-heading"><?= Html::encode($event->name) ?></h3>
                            <p class="list-group-item-text pull-left">
                            </p>
                            <p class="list-group-item-text">
                                <strong>Start: </strong><?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
                                <?= Html::encode($event->address) ?><br>
                                <small><?= Html::encode($event->user["username"]) ?> (<?= Html::encode(date("d.m.Y G:i", strtotime($event->creation_date))) ?>)</small>
                            </p>
                        </a>
                    </div>

                <?php endforeach; ?>

                <h4>New events</h4>

                <?php foreach ($newList as $event): ?>

                    <div class="list-group">
                        <a href="<?php echo \Yii::$app->request->BaseUrl.'/index.php?r=event/view&id='.$event->id; ?>" class="list-group-item">
                            <h3 class="list-group-item-heading"><?= Html::encode($event->name) ?></h3>
                            <p class="list-group-item-text pull-left">
                            </p>
                            <p class="list-group-item-text">
                                <strong>Start: </strong><?= Html::encode(date("d.m.Y G:i", strtotime($event->start_date))) ?><br>
                                <?= Html::encode($event->address) ?><br>
                                <small><?= Html::encode($event->user["username"]) ?> (<?= Html::encode(date("d.m.Y G:i", strtotime($event->creation_date))) ?>)</small>
                            </p>
                        </a>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    </div>
</div>

<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
</script>

