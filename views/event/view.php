<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Event */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-8">
            <div class="col-md-12">
                <div class="span12 map_searchEvent" id="map"></div>
            </div>
            <hr>

            <div class="col-md-12">
                <hr>

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


                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'creation_date',
                        'address',
                        'start_date',
                        'end_date',
                        'image',
                        'facebook',
                        'twitter',
                        'goabase',
                        'flickr',
                        'clicks',
                        'description',
                    ],
                ]) ?>
            </div>

        </div>

        <div class="col-md-4">

            <?php
            if (isset($socialmedia['images'])) {
            foreach ($socialmedia['images'] as $socialmedia_image): ?>
                <div class="col-md-6">
                    <a href="<?= $socialmedia_image['original'] ?>"><img src="<?= $socialmedia_image['thumbnail'] ?>" alt="img" /> </a>
                </div>
            <?php endforeach; }?>

        </div>

    </div>


</div>
