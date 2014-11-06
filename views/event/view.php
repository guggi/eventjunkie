<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$jsonMarkerList[0] = ['latitude' => $model->latitude, 'longitude' => $model->longitude]
?>

<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">

            <?php
            if ($model->user_id === Yii::$app->user->id) {
                ?>
                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>
            <?php } ?>
            <img class="img-responsive" src="images/<?= Html::encode($model->image) ?>" alt="">
            <hr>
            <p>
                <strong>Organizer:</strong>
                <?= Html::encode($model->user_id) ?> <tiny><?= Html::encode(date("d.m.Y G:i", strtotime($model->creation_date))) ?></tiny>
            </p>
            <p>
                <strong>Date:</strong>
                <?= date("d.m.Y G:i",
                    strtotime($model->start_date)) ?>
                <?= $model->start_date !== $model->end_date ? "-" : ""?>
                <?= $model->start_date !== $model->end_date ?
                    Html::encode(date("d.m.Y G:i", strtotime($model->end_date))) : "" ?>
            </p>
            <p>
                <strong>Address:</strong>
                <?= Html::encode($model->address) ?>
            </p>
            <hr>

            <div class="span12 map_searchEvent" id="map"></div>

            <hr>
            <p><strong>Description:</strong></p>
            <p><?= Html::encode($model->description) ?></p>




            <?= ""/* DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table borderless'],
                    'attributes' => [
                        'creation_date',
                        'address',
                        'start_date',
                        'end_date',
                        'image',
                        'facebook',
                        'twitter',
                        'flickr',
                        'clicks',
                        'description',
                    ],
                ]) */?>


            <hr>

            <!-- Only show if event is linked with either facebook, flickr or twitter -->
            <?php if ($model->facebook || $model->flickr || $model->twitter) { ?>

            <p class="tiny text-center">Linked with:
                <a class="btn btn-social-icon btn-twitter">
                    <i class="fa fa-twitter"></i>
                </a>
            </p>

            <?php } ?>
            <!-- -->

            <hr>

            <h4>Comments</h4>
            <?php
            for ($i = 0; $i < 5; $i++) { ?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <strong>Lorem Ipsum</strong><br>
                        <small class="text-muted">Date</small>
                        <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
                        </p>
                        <small> – Person on <cite title="source">Facebook</cite></small>
                    </div></div>
            <?php } ?>




        </div>

        <div class="col-md-4">

            <?php
            if (isset($socialmedia['images'])) { ?>

                <?php
                foreach ($socialmedia['images'] as $socialmedia_image): ?>
                    <div class="col-md-6">
                        <a href="<?= $socialmedia_image['original'] ?>"><img src="<?= $socialmedia_image['thumbnail'] ?>" alt="img" /> </a>
                    </div>
                <?php endforeach; ?>>
            <?php } ?>

        </div>
    </div>
</div>

<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
    var streetZoom = 15;
</script>
