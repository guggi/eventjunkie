<?php

namespace app\models;

use yii\base\Model;
use app\models\Event;

class SearchEventForm extends Event {
    public $from_date;
    public $to_date;
    public $radius = 0;
    public $type = [0,1];
    public $eventNameList = array();

    public function rules() {
        return [
            [['name', 'address'], 'string', 'max' => 50],
            [['address'], 'isValidGeoLocation'],
            [['radius'], 'integer'],
            [['from_date', 'to_date'], 'isValidDate'],
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
