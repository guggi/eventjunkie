<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = 'View Gallery from Event: ' . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Gallery';
?>

<link rel="stylesheet" href="css/blueimp-gallery.min.css">

<div class="event-gallery">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-12">

            <!-- images from linked sites -->


            <?php
            if (isset($socialmedia['images'])) { ?>
                <div class="col-md-12">
                    <div id="links">
                        <?php
                        foreach ($socialmedia['images'] as $socialmedia_image) { ?>
                            <div class="col-md-2">
                                <a href="<?= $socialmedia_image['original'] ?>">
                                    <img class="thumbnail" src="<?= $socialmedia_image['thumbnail'] ?>" alt="<?= $socialmedia_image['thumbnail'] ?>" />
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
