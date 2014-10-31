<?php

namespace app\models;

use yii\base\Model;

class CreateEventForm extends Model {
    public $name;
    public $address;
    public $start_date;
    public $end_date;
    public $image;
    public $description;

    public function rules() {
        return [
            [['name', 'address', 'start_date'], 'required'],
            [['description'], 'safe'],
        ];
    }
}
