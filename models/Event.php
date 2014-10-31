<?php

namespace app\models;

use Yii;

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
 * @property string $goabase
 * @property string $flickr
 * @property integer $clicks
 * @property string $description
 *
 * @property User $user
 */
class Event extends \yii\db\ActiveRecord
{
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
            [['creation_date', 'end_date'], 'safe'],
            [['name', 'address', 'start_date'], 'required'],
            [['latitude', 'longitude'], 'number'],
            [['name', 'address'], 'string', 'max' => 50],
            [['image'], 'string', 'max' => 100],
            [['facebook', 'twitter', 'goabase', 'flickr', 'description'], 'string', 'max' => 1000]
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
            'creation_date' => 'Creation Date',
            'name' => 'Name',
            'address' => 'Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'image' => 'Image',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter',
            'goabase' => 'Goabase',
            'flickr' => 'Flickr',
            'clicks' => 'Clicks',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
