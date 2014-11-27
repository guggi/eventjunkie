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
        }

        /*if (isset($this->socialmedia['images'])) {
            array_merge($this->socialmedia['images'], $images);
        } else {
            $this->socialmedia['images'] = $images;
        }*/
    }

    public function validateSocialMedia($link) {
        if ($link->site_name === 'flickr') {
            $flickrApi = new FlickrApi($link->url);
            $flickrApi->getImages();
        }
    }

    public function pushImages($loadedImages) {
        foreach ($loadedImages as $loadedImage) {
            $this->socialmedia['images'][] = $loadedImage;
        }
    }

    public function getSocialMedia() {
        return $this->socialmedia;
    }

} 