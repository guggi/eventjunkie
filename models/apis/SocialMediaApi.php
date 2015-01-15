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
        } else if ($link->site_name === 'facebook') {
            $facebookApi = new FacebookApi($link->url);
            $this->pushComments($facebookApi->getComments());
            $this->pushImages($facebookApi->getImages());
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
        if ($link->site_name === 'facebook') {
            $facebookApi = new FacebookApi($link->url);
            $facebookApi->getComments();
        }
    }

    public function pushImages($loadedImages) {
        if (isset($loadedImages)) {
            foreach ($loadedImages as $loadedImage) {
                $this->socialmedia['images'][] = $loadedImage;
            }
        }
    }

    public function pushComments($loadedComments) {
        if (isset($loadedComments)) {
            foreach ($loadedComments as $loadedComment) {
                $this->socialmedia['comments'][] = $loadedComment;
            }
        }
    }

    public function getSocialMedia() {
        if (isset($this->socialmedia['images'])) {
            $this->socialmedia['images'] = array_map("unserialize", array_unique(array_map("serialize", $this->socialmedia['images'])));
        }

        if (isset($this->socialmedia['comments'])) {
            usort($this->socialmedia['comments'], function ($a, $b) {
                if ($a['date'] == $b['date'])
                    return 0;
                return $a['date'] < $b['date'] ? 1 : -1;
            });
        }
        return $this->socialmedia;
    }
} 