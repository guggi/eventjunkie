<?php

namespace app\models\forms;

use yii\base\Model;
use app\models\Event;

class EventSearchForm extends Event {
    public $from_date;
    public $to_date;
    public $radius = 0;
    public $type_site = 1;
    public $type_goabase = 0;
    public $eventNameList = array();

    public function rules() {
        return [
            [['name', 'address'], 'string', 'max' => 50],
            [['address'], 'isValidGeoLocation'],
            [['radius', 'type_site', 'type_goabase'], 'integer'],
            [['from_date', 'to_date'], 'match',
                'pattern'=>'/^((\d{2})\.(\d{2})\.(\d{4})(\d{1,2}):(\d{2}))||((\d{4})-(\d{2})-(\d{2}) (\d{1,2}):(\d{2}):(\d{2}))$/'],
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
            'type_site' => 'Site',
            'type_goabase' => 'GoaBase',
        ];
    }
}
