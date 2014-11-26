<?php

namespace app\models;

use amnah\yii2\user\models\User;
use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "socialmedia".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $site_name
 * @property string $url
 * @property Event $event
 */
class SocialMedia extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'socialmedia';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'event_id'], 'integer'],
            [['site_name'], 'string', 'max' => 200],
            [['url'], 'string', 'max' => 500],
            [['url'], 'isValidUrl'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'url' => 'Social Media'
        ];
    }

    public function isValidUrl($attribute) {
        $flickr_regex = '/(http|https)?(:)?(\/\/)?(w*\.)?flickr\.com\/photos([^?]*)/';
        $check_head = curl_init($this->$attribute);
        curl_setopt($check_head, CURLOPT_NOBODY, true);
        curl_exec($check_head);

        if (!preg_match($flickr_regex, $this->$attribute) && (curl_getinfo($check_head, CURLINFO_HTTP_CODE) !== '200')) {
            $this->addError($attribute, 'Not a valid Url.');
        } else {
            $this->site_name = 'flickr';
        }

        curl_close($check_head);
    }
}