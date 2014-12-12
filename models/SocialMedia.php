<?php

namespace app\models;

use amnah\yii2\user\models\User;
use app\models\apis\SocialMediaApi;
use Yii;
use yii\base\Exception;
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
        $twitter_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
        $facebook_regex = '/(http|https)?:?(\/\/)?(w*\.)?facebook\.com\/events([^?]*)/';
        $hashtag_regex = '/(\#)?[a-zA-Z0-9\_\-]*/';
        $check_head = curl_init($this->$attribute);
        curl_setopt($check_head, CURLOPT_NOBODY, true);
        curl_exec($check_head);

        if ((curl_getinfo($check_head, CURLINFO_HTTP_CODE) === 200 || 302)) {
            if (preg_match($flickr_regex, $this->$attribute)) {
                $this->site_name = 'flickr';
            } else if (preg_match($twitter_regex, $this->$attribute)) {
                $this->site_name = 'twitter';
            } else if (preg_match($facebook_regex, $this->$attribute)) {
                $this->site_name = 'facebook';
            }
        } else {
            if (preg_match($hashtag_regex, $this->$attribute)) {
                $this->site_name = 'twitter';
            } else {
                $this->addError($attribute, 'Not a valid Url.');
            }
        }

        // check if the url returns valid images
        $socialMediaApi = new SocialMediaApi();
        try {
            $socialMediaApi->validateSocialMedia($this);
        } catch (\InvalidArgumentException $e) {
            $this->addError($attribute, $e->getMessage());
        }

        curl_close($check_head);
    }

    public function getType() {
        return ['type'];
    }
}