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


<div class="event-gallery">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-12">

            <!-- images from linked sites -->
            <?php
            if (isset($socialmedia['images'])) { ?>
                <div class="col-md-12">
                <?php
                $i = 0;
                foreach ($socialmedia['images'] as $socialmedia_image) { ?>
                    <div class="col-md-2">
                        <a href="<?= $socialmedia_image['original'] ?>">
                            <img class="thumbnail" src="<?= $socialmedia_image['thumbnail'] ?>" alt="<?= $socialmedia_image['thumbnail'] ?>" />
                        </a>
                    </div>
                    <?php
                    $i++;
                    if ($i > 0 && ($i % 6 === 0)) { ?>
                        </div>
                        <div class="col-md-12">
                    <?php }?>

                <?php } ?>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
</div>
