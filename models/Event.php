<?php

namespace app\models;

use amnah\yii2\user\models\User;
use Yii;
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
 * @property string $facebook
 * @property string $twitter
 * @property string $flickr
 * @property integer $clicks
 * @property string $description
 *
 * @property User $user
 */
class Event extends ActiveRecord
{
    public $upload_image;

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
            [['user_id', 'clicks'], 'integer'],
            [['end_date'], 'safe'],
            [['start_date', 'end_date'], 'isValidDate'],
            [['start_date'], 'isValidStartDate'], //todo entweder das start_date richtig validieren oder so lassen
            [['end_date'], 'isValidEndDate'],
            [['name', 'address', 'start_date'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'address'], 'string', 'max' => 50],
            [['address'], 'setGeoLocation'],
            [['image'], 'string', 'max' => 100],
            [['upload_image'], 'safe'],
            [['upload_image'], 'file', 'extensions' => 'jpg, gif, png', 'maxSize' => 2097152, 'tooBig' =>
            'Image size cannot be larger then 2MB.'],
            [['facebook', 'twitter', 'flickr', 'description'], 'string', 'max' => 1000], //todo hier validieren
            [['facebook', 'twitter', 'flickr'], 'isValidUrl'],
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
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'flickr' => 'Flickr',
            'clicks' => 'Clicks',
            'description' => 'Description',
        ];
    }

    public function isValidDate($attribute, $params)
    {
        if (!strtotime($this->$attribute)) {
            $this->addError($attribute, $attribute . ' has wrong format');
        }
    }

    public function isValidStartDate($attribute, $params)
    {
        if (strtotime($this->$attribute) <= 0) {
            $this->addError($attribute, $attribute . ' must be after '. date('Y-m-d H:i:s', 0));
        }
    }

    public function isValidEndDate($attribute, $params)
    {
        if (strtotime($this->$attribute) < strtotime($this->start_date)) {
            $this->addError($attribute, $attribute . ' must be after Start Date');
        }
    }

    public function isValidUrl($attribute, $params)
    {
        $flickr_regex = '/(http|https)?(:)?(\/\/)?(w*\.)?flickr\.com\/photos([^?]*)/';
        $check_head = curl_init($this->$attribute);
        curl_setopt($check_head, CURLOPT_NOBODY, true);
        curl_exec($check_head);

        if (!preg_match($flickr_regex, $this->$attribute) && (curl_getinfo($check_head, CURLINFO_HTTP_CODE) !== '200')) {
            $this->addError($attribute, 'Not a valid Url.');
        }

        curl_close($check_head);
    }

    /*
     * Validate address and set latitude and longitude via Google-Api.
     */
    public function setGeoLocation($attribute, $param)
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