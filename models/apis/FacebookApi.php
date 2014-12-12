<?php

namespace app\models\apis;

use Facebook\FacebookRequest;

class FacebookApi {
    private $url;
    private $data;

    function __construct($url) {
        $this->url = $url;
        //$id = $this->getID();
    }

    public function getComments() {

    }

    private function getID() {
        $substring = "https://www.facebook.com/events/";
        $string = substr($this->$url, strlen($substring));

        $id = preg_split("/\//", $string);

        return intval($id[0]);
    }
}