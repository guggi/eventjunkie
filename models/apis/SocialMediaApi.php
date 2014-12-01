<?php

namespace app\models\apis;

class SocialMediaApi {
    private $socialmedia;

    public function __construct() {
        $this->socialmedia = [
            ['images' => []],
            ['comments' => []],
        ];
    }

    public function loadSocialMedia($link) {
        if ($link->site_name === 'flickr') {
            $flickrApi = new FlickrApi($link->url);
            $this->pushImages($flickrApi->getImages());
        } else if ($link->site_name === 'twitter') {
            $twitterApi = new TwitterApi($link->url);
            $twitterApi->getTweets();
            $this->pushComments($twitterApi->getComments());
            $this->pushImages($twitterApi->getImages());
        }

    }

    public function validateSocialMedia($link) {
        if ($link->site_name === 'flickr') {
            $flickrApi = new FlickrApi($link->url);
            $flickrApi->getImages();
        }
        if ($link->site_name === 'twitter') {
            $twitterApi = new TwitterApi($link->url);
            $twitterApi->getTweets();
        }
    }

    public function pushImages($loadedImages) {
        foreach ($loadedImages as $loadedImage) {
            $this->socialmedia['images'][] = $loadedImage;
        }
    }

    public function pushComments($loadedComments) {
        $this->socialmedia['comments'] = [];
        foreach ($loadedComments as $loadedComment) {
            $this->socialmedia['comments'][] = $loadedComment;
        }
    }

    public function getSocialMedia() { // todo sort funktioniert nicht
        usort($this->socialmedia['comments'], function ($a, $b) {
            $t1 = strtotime($a['date']);
            $t2 = strtotime($b['date']);
            var_dump($t1);
            var_dump($t2);
            if($t1 == $t2)
                return 0;
            return $t1 < $t2 ? 1 : -1;
        });
        return $this->socialmedia;
    }
} 