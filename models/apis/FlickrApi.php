<?php

class FlickrApi {
    private $link;

    public function FlickrApi($link) {
        $this->link = $link;
    }

    public function getGalleryPhotos() {
        $id =
        $data = file_get_contents("
        https://api.flickr.com/services/rest/?method=flickr.galleries.getPhotos&
        api_key=9772b5c22b69edde40f5993d1790358e&gallery_id=15053256173&format=json");

    }

} 