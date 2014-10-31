<?php

namespace app\models\apis;

use Yii;

class FlickrApi {
    private $link;
    private $api_key;

    function __construct($link) {
        $this->link = $link;
        $this->api_key = '9772b5c22b69edde40f5993d1790358e';
    }

    public function getGalleryPhotos() {
        $images = [];

        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.urls.lookupGallery&' .
            'api_key=' . $this->api_key . '&' .
            'url=' . $this->link . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        $id = $data->{'gallery'}->{'id'};

        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.galleries.getPhotos&' .
            'api_key=' . $this->api_key . '&' .
            'gallery_id=' . $id . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        foreach ($data->{'photos'}->{'photo'} as $photo) {
            $json = file_get_contents('https://api.flickr.com/services/rest/?' .
                'method=flickr.photos.getSizes&' .
                '&api_key=' . $this->api_key . '&' .
                'photo_id=' . $photo->{'id'} . '&format=json&nojsoncallback=1');
            $data = json_decode($json);
            array_push($images, ['original' => $data->{'sizes'}->{'size'}[sizeof($data->{'sizes'}->{'size'})-1]->{'source'},
                'thumbnail' => $data->{'sizes'}->{'size'}[1]->{'source'}]);
        }
        return $images;
    }

} 