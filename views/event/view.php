<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="css/blueimp-gallery.min.css">

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
                if (\Yii::$app->user->id == $model->user_id) {
                    ?>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php } else { ?>
                    <?= Html::a('Link with Social Media', ['link', 'id' => $model->id], ['class' => 'btn btn-default']) ?>
                <?php } ?>
                <?php if (\Yii::$app->user->can('admin') || ($model->user_id == Yii::$app->user->id)) { ?>
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
            <div class="col-md-12">
                <?php if ($model->image) { ?>
                    <img class="img-responsive" src="<?= \Yii::$app->request->getBaseUrl() .'/'. Yii::$app->params['imagePath'] .
                    Html::encode($model->image) ?>">
                <?php } ?>
            </div>

            <hr>

            <!-- data -->

            <div class="col-md-12">
                <p>
                    <strong>Organizer:</strong>
                    <?= Html::encode($model->user["username"]) ?> <tiny><?= Html::encode(date("d.m.Y G:i", strtotime($model->creation_date))) ?></tiny>
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
                <p class="text-center"><b><?= Html::encode($model->clicks) ?></b> Views</p>

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
                        <div class="<?= ($socialmedia_comment['site_name'] == 'Twitter') ? 'panel panel-info' : 'panel panel-primary' ?>">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-1">
                                        <div class="btn btn-social-icon btn-<?= strtolower($socialmedia_comment['site_name']) ?>">
                                            <i class="fa fa-<?= strtolower($socialmedia_comment['site_name']) ?>">
                                            </i>
                                        </div>
                                    </div>
                                    <div class="col-md-11">
                                        <strong><a href="<?= $socialmedia_comment['url'] ?>"><?= $socialmedia_comment['title'] ?></a></strong><br>
                                        <small class="text-muted"><?= date("d.m.Y G:i",
                                                $socialmedia_comment['date']) ?></small>
                                    </div>
                                    <div class="col-md-12">
                                        <p><?= $socialmedia_comment['text'] ?>
                                        </p>
                                        <small> – <a href="<?= $socialmedia_comment['author_url'] ?>"><?= $socialmedia_comment['author'] ?></a>
                                            on <a href="<?= $socialmedia_comment['socialmedia_url'] ?>"><cite title="source"><?= $socialmedia_comment['site_name'] ?></cite></a></small>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="col-md-4">

            <!-- images from linked sites -->
            <?php
            if (isset($socialmedia['images'])) { ?>

                <div class="col-md-12"><a href="<?= \Yii::$app->request->BaseUrl.'/index.php?r=event/gallery&id='.$model->id ?>">View Gallery</a>
                    <hr></div>
                <div class="col-md-12">
                    <div id="links">
                        <?php
                        $i = 0;
                        $items = [];
                        foreach ($socialmedia['images'] as $socialmedia_image) { ?>
                            <div class="col-md-6">
                                <a href="<?= $socialmedia_image['original'] ?>">
                                    <img class="thumbnail thumbnail-image" src="<?= $socialmedia_image['thumbnail'] ?>" alt="<?= $socialmedia_image['thumbnail'] ?>" />
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>


<!-- The Gallery as lightbox dialog, should be a child element of the document body -->
<div id="blueimp-gallery" class="blueimp-gallery">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>


<script>
    var jsonMarkerList = JSON.parse('<?php echo json_encode($jsonMarkerList) ?>');
    var streetZoom = 15;
</script>

<script src="js/blueimp-gallery.min.js"></script>
<script>
    document.getElementById('links').onclick = function (event) {
        event = event || window.event;
        var target = event.target || event.srcElement,
            link = target.src ? target.parentNode : target,
            options = {index: link, event: event},
            links = this.getElementsByTagName('a');
        blueimp.Gallery(links, options);
    };
</script>
