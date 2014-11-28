<?php

namespace app\models\apis;

use Exception;
use TwitterAPIExchange;
use Yii;
use yii\authclient\clients\Twitter;
use yii\authclient\OAuthToken;

class TwitterApi
{
    private $twitter;
    private $url;
    private $images = [];
    private $comments = [];

    function __construct($url)
    {
        $settings = [
            'oauth_access_token' => Yii::$app->params['twitterAccessToken'],
            'oauth_access_token_secret' => Yii::$app->params['twitterAccessTokenSecret'],
            'consumer_key' => Yii::$app->params['twitterApiKey'],
            'consumer_secret' => Yii::$app->params['twitterApiSecret']
        ];
        $this->url = $url;
        $this->twitter = new TwitterAPIExchange($settings);
    }

    public function getImages() {
        return $this->images;
    }

    public function getComments() {
        return $this->comments;
    }

    public function getTweets()
    {
        try {
            $hashtag_regex = '/(\#)?[a-zA-Z0-9\_\-]*/';
            $url_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
            if (preg_match($url_regex, $this->url)) {
                preg_match($hashtag_regex, $this->url);
                $this->getTweetsPerHashtag($this->getHashtagFromUrl());
            } else if (preg_match($hashtag_regex, $this->url)) {
                $this->getTweetsPerHashtag($this->url);
            }
        } catch (Exception $e) {
            throw new \InvalidArgumentException('Invalid Twitter Url/Hashtag');
        }
    }

    public function getHashtagFromUrl() {
        $hashtag_regex = '/[^(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/)][a-zA-Z0-9\_\-]+/';
        preg_match($hashtag_regex, $this->url, $match);
        return $match[0];
    }

    public function getTweetsPerHashtag($hashtag) {
        if (substr($hashtag, 0, 1) === '#') {
            $hashtag = substr($hashtag, 1);
        }

        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q=#' . $hashtag;
        $requestMethod = 'GET';

        $json = $this->twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $data = json_decode($json);

        foreach ($data->{"statuses"} as $tweet) {
            array_push($this->comments,
                [
                    'title' => '#'.$hashtag,
                    'date' => strtotime($tweet->{"created_at"}),
                    'author' => $tweet->{"user"}->{"screen_name"},
                    'text' => $tweet->{"text"},
                    'site_name' => 'Twitter',
                    'url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"} .'/status/'.$tweet->{"id"},
                    'author_url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"},
                    'socialmedia_url' => 'https://twitter.com/hashtag/'. $hashtag . '?src=hash',
                ]);
            var_dump(strtotime($tweet->{"created_at"}));
            foreach ($tweet->{"entities"}->{"urls"} as $url) {
                array_push($this->images,
                    [
                        'thumbnail' => $url->{"url"},
                        'original' => $url->{"url"},
                    ]);
            }
        }
    }
}