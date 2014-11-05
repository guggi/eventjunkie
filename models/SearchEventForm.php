<?php

namespace app\models;

use yii\base\Model;
use app\models\Event;

class SearchEventForm extends Event {
    public $name;
    public $address;
    public $from_date;
    public $to_date;
    public $radius;
    public $type;

    public function rules() {
        return [
           //[["name"], "save"]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'address' => 'Address',
            'from_date' => 'From Date',
            'ro_date' => 'To Date',
            'radius' => 'Radius',
            'type' => 'Which Events',
        ];
    }
}
