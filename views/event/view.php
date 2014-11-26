<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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

<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">

            <!-- update / delete -->
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

            <!-- image -->
            <?php if ($model->image) { ?>
            <img class="img-responsive" src="<?= \Yii::$app->request->getBaseUrl() . Yii::$app->params['imagePath'] .
            Html::encode($model->image) ?>">
            <?php } ?>

            <hr>

            <!-- data -->
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

            <!-- google map -->
            <div class="span12 map_searchEvent" id="map"></div>

            <hr>

            <!-- description -->
            <p><strong>Description:</strong></p>
            <p><?= Html::encode($model->description) ?></p>

            <hr>

            <!-- clicks -->
            <p class="text-center"><?= Html::encode($model->clicks) ?> Clicks</p>

            <hr>

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
                        <a class="btn btn-social-icon btn-twitter">
                            <i class="fa fa-twitter"></i>
                        </a>
                    <?php } ?>
                    <?php } ?>
                </p>

            <?php }?>
            <!-- -->

            <hr>


            <h4>Comments</h4>

            <!-- comments from linked sites -->
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

            <!-- images from linked sites -->
            <?php
            if (isset($socialmedia['images'])) { ?>

                <?php
                foreach ($socialmedia['images'] as $socialmedia_image): ?>
                    <div class="col-md-6">
                        <a href="<?= $socialmedia_image['original'] ?>"><img src="<?= $socialmedia_image['thumbnail'] ?>" alt="img" /> </a>
                    </div>
                <?php endforeach; ?>
            <?php } ?>

        </div>
    </div>
</div>

<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
    var streetZoom = 15;
</script>
