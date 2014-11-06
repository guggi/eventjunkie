<?php

namespace app\models\apis;

use Yii;

class FlickrApi {
    private $api_key;
    private $link;
    private $images = [];

    function __construct($link) {
        $this->api_key = 'e9eef2cb072ac056df7140676c49683b';
        $this->link = $link;
    }

    /*
     * Checks the type of link gallery/set/...
     */
    public function getPhotos() {
        $gallery_regex = '/(http|https)?:?(\/\/)?(w*\.)?flickr\.com\/photos\/flickr\/galleries\/([^?]*)/';
        $set_regex = '/(http|https)?:?(\/\/)?(w*\.)?flickr\.com\/photos\/(([0-9]*\@N[0-9][0-9])|(flickr))\/sets\/([0-9]*)\/?/';
        if (preg_match($gallery_regex, $this->link)) {
            $this->getGalleryPhotos();
        } else if (preg_match($set_regex, $this->link)){
            $this->getSetPhotos();
        }
        return $this->images;
    }

    /*
     * Calls gallery functions.
     */
    public function getGalleryPhotos() {
        $id = $this->getGalleryId();
        $photos = $this->getGalleryPhotosId($id);
        foreach ($photos as $photo) {
            $this->getPhoto($photo->{'id'});
        }
    }

    /*
     * Return the id of the gallery by a given Flickr-Url.
     */
    public function getGalleryId() {
        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.urls.lookupGallery&' .
            'api_key=' . $this->api_key . '&' .
            'url=' . $this->link . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        if ($data->{'stat'} === 'fail') {
            throw new \InvalidArgumentException('Invalid Flickr Gallery');
        }
        return $data->{'gallery'}->{'id'};
    }

    /*
     * Return all gallery photos with their id.
     */
    public function getGalleryPhotosId($id) {
        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.galleries.getPhotos&' .
            'api_key=' . $this->api_key . '&' .
            'gallery_id=' . $id . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        $photos = $data->{'photos'}->{'photo'};

        $numPages = $data->{'photos'}->{'pages'};
        for ($i = 2; $i <= $numPages; $i++) {
            $json = file_get_contents('https://api.flickr.com/services/rest/?' .
                'method=flickr.galleries.getPhotos&' .
                'api_key=' . $this->api_key . '&' .
                'gallery_id=' . $id . '&' .
                '&page=' . $i . '&' .
                'format=json&nojsoncallback=1');
            $data = json_decode($json);
            array_merge($photos, $data->{'photos'}->{'photo'});
        }
        return $photos;
    }

    public function getSetPhotos() {
        $id = $this->getSetId();
        $photos = $this->getSetPhotosId($id);
        foreach ($photos as $photo) {
            $this->getPhoto($photo->{'id'});
        }
    }

    public function getSetId() {
        $id_regex = '/[0-9]{9,}/';
        preg_match($id_regex, $this->link, $match);
        if (!$match) {
            throw new \InvalidArgumentException('Invalid Flickr Set');
        }

        return $match[0];
    }

    /*
     * Return all gallery photos with their id.
     */
    public function getSetPhotosId($id) {
        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.photosets.getPhotos&' .
            'api_key=' . $this->api_key . '&' .
            'photoset_id=' . $id . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        $photos = $data->{'photoset'}->{'photo'};

        $numPages = $data->{'photoset'}->{'pages'};
        for ($i = 2; $i <= $numPages; $i++) {
            $json = file_get_contents('https://api.flickr.com/services/rest/?' .
                'method=flickr.photosets.getPhotos&' .
                'api_key=' . $this->api_key . '&' .
                'photoset_id=' . $id . '&' .
                '&page=' . $i . '&' .
                'format=json&nojsoncallback=1');
            $data = json_decode($json);
            array_merge($photos, $data->{'photoset'}->{'photo'});
        }
        return $photos;
    }

    /*
     * Extract the Photo-Url by the photo id.
     */
    public function getPhoto($id) {
        $json = file_get_contents('https://api.flickr.com/services/rest/?' .
            'method=flickr.photos.getSizes&' .
            '&api_key=' . $this->api_key . '&' .
            'photo_id=' . $id . '&format=json&nojsoncallback=1');
        $data = json_decode($json);
        array_push($this->images, ['original' => $data->{'sizes'}->{'size'}[sizeof($data->{'sizes'}->{'size'})-1]->{'source'},
            'thumbnail' => $data->{'sizes'}->{'size'}[1]->{'source'}]);
    }

} 