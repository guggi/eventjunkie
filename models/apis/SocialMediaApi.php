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
        foreach ($loadedComments as $loadedComment) {
            $this->socialmedia['comments'][] = $loadedComment;
        }
    }

    public function getSocialMedia() {
        return $this->socialmedia;
    }

} 