<?php

class SocialMediaApi {
    private $socialmedia = [
        ['images' => []],
        ['comments' => []],
    ];

    public function loadSocialMedia($link) {
        $type = getSocialMediaType($link);
        if ($type === 'flickr') {
            $flickrApi = new FlickrApi($link);
            //$this->socialmedia["images" ] => $flickrApi->getGalleryPhotos();
        }
    }

    public function getSocialMediaType($link) {
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