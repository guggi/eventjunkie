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
        $type = $this->getSocialMediaType($link);
        if ($type === 'flickr') {
            $flickrApi = new FlickrApi($link);
            if (isset($this->socialmedia['images'])) {
                array_merge(array($this->socialmedia['images']), $flickrApi->getGalleryPhotos());
            } else {
                $this->socialmedia['images'] = $flickrApi->getGalleryPhotos();
            }
        }
    }

    private function getSocialMediaType($link) {
        if (strpos($link,'www.facebook.com/events') !== false) {
            return 'facebook';
        } else if (strpos($link,'www.flickr.com/photos/flickr/galleries') !== false) {
            return 'flickr';
        } else {
            return false;
        }
    }

    public function getSocialMedia() {
        return $this->socialmedia;
    }

} 