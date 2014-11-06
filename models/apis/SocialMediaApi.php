<?php

namespace app\models\apis;

class SocialMediaApi {
    private $socialmedia;
    private $flickr_regex = '/(http|https)?(:)?(\/\/)?(w*\.)?flickr\.com\/photos([^?]*)/';

    public function __construct() {
        $this->socialmedia = [
            ['images' => []],
            ['comments' => []],
        ];
    }

    public function loadSocialMedia($link) {
        $images = [];
        if (preg_match($this->flickr_regex, $link)) {
            $flickrApi = new FlickrApi($link);
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