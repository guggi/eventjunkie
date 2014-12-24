<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Event */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'List';
$this->params['breadcrumbs'][] = ['label' => 'Events', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'List';
?>

<div class="event-list">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (!\Yii::$app->user->can('admin')) { ?>
        <p>

            <?= Html::a('Create event', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            'address',
            'creation_date',
            'start_date',
            'end_date',
            /*        'id',
                    [
                        'attribute' => 'role_id',
                        'label' => Yii::t('user', 'Role'),
                        'filter' => $role::dropdown(),
                        'value' => function($model, $index, $dataColumn) use ($role) {
                            $roleDropdown = $role::dropdown();
                            return $roleDropdown[$model->role_id];
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'label' => Yii::t('user', 'Status'),
                        'filter' => $user::statusDropdown(),
                        'value' => function($model, $index, $dataColumn) use ($user) {
                            $statusDropdown = $user::statusDropdown();
                            return $statusDropdown[$model->status];
                        },
                    ],*/

            //'profile.full_name',
            // 'new_email:email',
            // 'username',
            // 'password',
            // 'auth_key',
            // 'api_key',
            // 'login_ip',
            // 'login_time',
            // 'create_ip',
            // 'create_time',
            // 'update_time',
            // 'ban_time',
            // 'ban_reason',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        if(!\Yii::$app->user->can('admin')) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                                'title' => Yii::t('yii', 'Update'),
                            ]);
                        }
                    }

                ],
            ],
        ],
    ]); ?>
</div>