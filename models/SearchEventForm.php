<?php

namespace app\models;

use yii\base\Model;

class SearchEventForm extends Model {
    public $name;
    public $address;
    public $fromDate;
    public $toDate;
    public $distance;

    public function rules() {
        return [
           // [["name"], "required"]
        ];
    }
}
