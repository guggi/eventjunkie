<?php

namespace app\models\apis;

use Exception;
use TwitterAPIExchange;
use Yii;

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
            $user_regex = '/(\@)[a-zA-Z0-9\_\-]*/';
            $url_regex = '/(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/[a-zA-Z0-9\_\-]*\?src=hash/';
            if (preg_match($url_regex, $this->url)) {
                preg_match($hashtag_regex, $this->url);
                $this->getTweetsPerHashtag($this->getHashtagFromUrl());
            } else if (preg_match($user_regex, $this->url)) {
                $this->getTweetsPerUser($this->url);
            } else if (preg_match($hashtag_regex, $this->url)) {
                $this->getTweetsPerHashtag($this->url);
            }
        } catch (Exception $e) {
            throw new \InvalidArgumentException('Invalid Twitter Url/Hashtag.');
        }
    }

    private function getHashtagFromUrl() {
        $hashtag_regex = '/[^(http|https)?:?(\/\/)?(w*\.)?twitter\.com\/hashtag\/)][a-zA-Z0-9\_\-]+/';
        preg_match($hashtag_regex, $this->url, $match);
        return $match[0];
    }

    private function parseTweet($tweet_text) {
        $parsed_tweet_text = preg_replace(
            '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@',
            '<a href="$1">$1</a>',
            $tweet_text);
        $parsed_tweet_text = preg_replace(
            '/@([\w]+)/',
            '<a href="https://twitter.com/$1">@$1</a>',
            $parsed_tweet_text);
        $parsed_tweet_text = preg_replace(
            '/s*#([\wäÄöÖüÜß]+)/',
            ' <a href="https://twitter.com/hashtag/$1?src=hash">#$1</a>',
            $parsed_tweet_text);
        return $parsed_tweet_text;
    }

    private function getTweetsPerHashtag($hashtag) {
        if (substr($hashtag, 0, 1) === '#') {
            $hashtag = substr($hashtag, 1);
        }

        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getfield = '?q=#' . $hashtag . '&result_type=recent&count=100';
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
                    'text' => $this->parseTweet($tweet->{"text"}),
                    'site_name' => 'Twitter',
                    'url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"} .'/status/'.$tweet->{"id"},
                    'author_url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"},
                    'socialmedia_url' => 'https://twitter.com/hashtag/'. $hashtag . '?src=hash',
                ]);

            if (isset($tweet->{"entities"}->{"media"})) {
                foreach ($tweet->{"entities"}->{"media"} as $media) {
                    array_push($this->images,
                        [
                            'thumbnail' => $media->{"media_url"} . ':thumb',
                            'original' => $media->{"media_url"},
                        ]);

                }
            }
        }
    }

    private function getTweetsPerUser($user) {
        if (substr($user, 0, 1) === '@') {
            $user = substr($user, 1);
        }

        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name=' . $user . '&count=100';
        $requestMethod = 'GET';

        $json = $this->twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $data = json_decode($json);

        foreach ($data as $tweet) {
            array_push($this->comments,
                [
                    'title' => '@'.$user,
                    'date' => strtotime($tweet->{"created_at"}),
                    'author' => $tweet->{"user"}->{"screen_name"},
                    'text' => $this->parseTweet($tweet->{"text"}),
                    'site_name' => 'Twitter',
                    'url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"} .'/status/'.$tweet->{"id"},
                    'author_url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"},
                    'socialmedia_url' => 'https://twitter.com/'. $tweet->{"user"}->{"screen_name"},
                ]);

            if (isset($tweet->{"entities"}->{"media"})) {
                foreach ($tweet->{"entities"}->{"media"} as $media) {
                    array_push($this->images,
                        [
                            'thumbnail' => $media->{"media_url"} . ':thumb',
                            'original' => $media->{"media_url"},
                        ]);

                }
            }
        }
    }
}