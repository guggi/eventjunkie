<?php

namespace app\models;

use yii\base\Model;

class SearchEventForm extends Model {
    public $name;
    public $address;
    public $from_date;
    public $to_date;
    public $distance;

    public function rules() {
        return [
           //[["name"], "save"]
        ];
    }
}
