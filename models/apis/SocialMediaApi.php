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
        $images = [];
        if ($link->site_name === 'flickr') {
            $flickrApi = new FlickrApi($link->url);
            $images = $flickrApi->getPhotos();
        }

        if (isset($this->socialmedia['images'])) {
            array_merge($this->socialmedia['images'], $images);
        } else {
            $this->socialmedia['images'] = $images;
        }
    }

    public function getSocialMedia() {
        return $this->socialmedia;
    }

} 