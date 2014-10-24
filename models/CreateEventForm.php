<?php

namespace app\models;

use yii\base\Model;

class CreateEventForm extends Model {
    public $name;
    public $address;
    public $eventDate;
    public $description;

    public function rules() {
        return [
            [["name"], "required"]
        ];
    }
}
