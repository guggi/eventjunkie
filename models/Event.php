<?php

namespace app\models;

use amnah\yii2\user\models\User;
use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $creation_date
 * @property string $name
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property string $start_date
 * @property string $end_date
 * @property string $image
 * @property integer $clicks
 * @property string $description
 * @property string $note
 *
 * @property User $user
 */
class Event extends ActiveRecord
{
    public $upload_image;
    public $num_socialMedia;
    public $max_num_socialMedia;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address', 'start_date'], 'required'],
            [['user_id', 'clicks'], 'integer'],

            // Validate Dates
            [['start_date', 'end_date'], 'match',
                'pattern'=>'/^((\d{2})\.(\d{2})\.(\d{4})(\d{1,2}):(\d{2}))||((\d{4})-(\d{2})-(\d{2}) (\d{1,2}):(\d{2}):(\d{2}))$/'],
            [['start_date', 'end_date'], 'isValidDate'],
            [['start_date'], 'isValidStartDate'],
            [['end_date'], 'isValidEndDate'],

            // Validate Address
            [['name', 'address'], 'string', 'max' => 50],
            [['address'], 'isValidGeoLocation'],
            [['latitude', 'longitude'], 'number'],

            // Validate Image
            [['image'], 'string', 'max' => 100],
            [['upload_image'], 'file', 'extensions' => 'jpg, gif, png', 'maxSize' => 2097152, 'tooBig' =>
                'Image size cannot be larger then 2MB.'],

            [['description', 'note'], 'string', 'max' => 1000],
            [['num_socialMedia', 'max_num_socialMedia'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'image' => 'Image',
            'socialmedia' => 'Social Media',
            'clicks' => 'Clicks',
            'description' => 'Description',
            'note' => 'Note',
        ];
    }

    /**
     * @param $attribute
     */
    public function isValidDate($attribute)
    {
        if (!strtotime($this->$attribute) && strtotime($this->$attribute) != 0) {
            $this->addError($attribute, $attribute . ' has wrong format');
        }
    }

    /**
     * @param $attribute
     */
    public function isValidStartDate($attribute)
    {
        if (strtotime($this->$attribute) <= 0) {
            $this->addError($attribute, 'Start date must be after '. date('d.m.Y G:i', 0));
        }
    }

    /**
     * @param $attribute
     */
    public function isValidEndDate($attribute)
    {
        if (strtotime($this->$attribute) > 0 && strtotime($this->$attribute) < strtotime($this->start_date)) {
            $this->addError($attribute, 'End Date must be after Start Date');
        }
    }

    public function afterValidate() {
        $this->start_date = date('Y-m-d H:i:s', strtotime($this->start_date));
        if (strtotime($this->end_date) === 0) {
            $this->end_date = date('Y-m-d H:i:s', strtotime($this->start_date));
        } else {
            $this->end_date = date('Y-m-d H:i:s', strtotime($this->end_date));
        }
    }

    /*
     * Validate address and set latitude and longitude via Google-Api.
     */
    public function isValidGeoLocation($attribute)
    {
        if ($this->$attribute !== '') {
            $parsed_address = str_replace(' ', '+', $this->$attribute);
            $jsonData = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' .
                $parsed_address . '&sensor=true');
            $data = json_decode($jsonData);
            if ($data->{'status'} != 'OK') {
                $this->addError($attribute, $attribute . ' ' . $this->$attribute . ' doesn\'t exist');
                return;
            }
            $this->latitude = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $this->longitude = $data->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Get path of image.
     *
     * @return string or null
     */
    public function getImagePath()
    {
        if (isset($this->upload_image)) {
            return Yii::$app->params['imagePath'] . $this->image;
        } else {
            return null;
        }
    }

    /**
     * Get upload instance.
     *
     * @return string or null
     */
    public function uploadImage()
    {
        if ($upload_image = UploadedFile::getInstance($this, 'upload_image')) {
            $this->image = Yii::$app->security->generateRandomString() . '.' . $upload_image->extension;
        }

        return $upload_image;
    }

    /**
     * Delete image.
     *
     * @return boolean the status of deletion
     */
    public function deleteImage($delete_image)
    {
        if (!$delete_image) {
            return false;
        }

        $file = Yii::$app->params['imagePath'] . $delete_image;

        if (empty($file) || !file_exists($file)) {
            return false;
        }

        if (!unlink($file)) {
            return false;
        }

        return true;
    }
}
