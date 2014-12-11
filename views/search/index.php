<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
?>
<h1>Search results</h1>
<br>

<i>Gesucht wurde nach: ' 
<?php 

if( isset($searchModel) ){
	echo $searchModel->name;
	if( $searchModel->address!= "") echo "  ".$searchModel->address;
}

?>
 '
</i>

<br><br>

<p>
<?php if( count($results) ==0) echo "<i>No parties founded... :(</i><br><br>";?>
<?php foreach ($results as $event): ?>
<?php try{ ?>
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

</p>
