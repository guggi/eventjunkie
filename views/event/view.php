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
            <p>
                <?php
                if (Yii::$app->user->id === $model->user_id) {
                    ?>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php } else { ?>
                    <?= Html::a('Link with Social Media', ['link', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                <?php } ?>
                <?php if (Yii::$app->user->can('admin') || $model->user_id === Yii::$app->user->id) { ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>

                <?php } ?>
            </p>

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
            <br>
            <p><i>Note:</i></p>
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
                            <a class="btn btn-social-icon btn-facebook" href="<?= $socialMediaModel->url ?>">
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
            <!-- -->

            <hr>

            <h4>Comments</h4>

            <!-- comments from linked sites -->
            <?php
            if (isset($socialmedia['comments'])) { ?>
                <?php foreach ($socialmedia['comments'] as $socialmedia_comment) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <strong><a href="<?= $socialmedia_comment['url'] ?>"><?= $socialmedia_comment['title'] ?></a></strong><br>
                            <small class="text-muted"><?= date("d.m.Y G:i",
                                    $socialmedia_comment['date']) ?></small>
                            <p><?= $socialmedia_comment['text'] ?>
                            </p>
                            <small> – <a href="<?= $socialmedia_comment['author_url'] ?>"><?= $socialmedia_comment['author'] ?></a>
                                on <a href="<?= $socialmedia_comment['socialmedia_url'] ?>"><cite title="source"><?= $socialmedia_comment['site_name'] ?></cite></a></small>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-md-4">


            <!-- images from linked sites -->
            <?php
            if (isset($socialmedia['images'])) { ?>
                <div class="col-md-12"><a href="<?= \Yii::$app->request->BaseUrl.'/index.php?r=event/gallery&id='.$model->id ?>">View Gallery</a>
                    <hr></div>
                <div class="col-md-12">
                <?php
                $i = 0;
                foreach ($socialmedia['images'] as $socialmedia_image) { ?>
                    <div class="col-md-6">
                        <a href="<?= $socialmedia_image['original'] ?>">
                            <img class="thumbnail" src="<?= $socialmedia_image['thumbnail'] ?>" alt="<?= $socialmedia_image['thumbnail'] ?>" />
                        </a>
                    </div>
                    <?php
                    $i++;
                    if ($i > 0 && ($i % 2 === 0)) { ?>
                        </div>
                        <div class="col-md-12">
                    <?php }?>

                <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
    var streetZoom = 15;
</script>

<script src="/lightbox/js/jquery-1.11.0.min.js"></script>
<script src="/lightbox/js/lightbox.min.js"></script>
<link href="/lightbox/css/lightbox.css" rel="stylesheet" />