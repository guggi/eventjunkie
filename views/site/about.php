<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Event Junkie';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
<br>

<?php echo "<br><img src=".\Yii::$app->request->BaseUrl."/info.jpg"."  style='max-heigth:100px;'/>" ?>
<br>
<br>
EventJunkie ist ein Eventkalender, welcher mit Facebook (www.facebook.com),
Goabase, Flickr und Twitter verküpft ist. <br>
Die Hauptseite enthält eine Google-Karte, welche die Adresse der Events anzeigt, 
die direkt unter der Karte angezeigt werden. <br>
Die Events werden direkt auf EventJunkie erstellt und anschließend mit Facebook, Flickr oder Twitter verlinkt. <br>
So könnten etwa Bilder von Flickr oder Kommentare von Facebook oder Twitter zusätzlich im Event angezeigt werden. <br>
Auch wenn noch keine Parties im Kalender eingetragen sind, werden aktuelle Parties von Goabase im Kalender angezeigt. <br>
Man kann Informationen zu den Events auch ohne einen Account sehen. <br>
Jedoch können Events nur dann erstellt werden, wenn man einen gültigen Account bei EventJunkie hat. <br>
Zu diesem Zweck kann man sich jederzeit mit seiner Email Adresse registrieren. <br>
EventJunkie soll ein breites Publikum ansprechen. <br>
Die Zielgruppe sind Leute die gerne auf Parties gehen und auf einer eigenen Seite, gesammelte Informatio-
nen zu Parties erhalten wollen. <br>
Diese Event Plattform soll kein soziales Netzwerk darstellen, weshalb Kommunikation zwischen den einzelnen Benutzern nicht möglich ist.

</div>
