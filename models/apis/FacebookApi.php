<?php

namespace app\models\apis;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;

class FacebookApi {
    private $url;
    private $data;
    private $session;
    private $graphObject;

    function __construct($url) {
        $this->url = $url;
        FacebookSession::setDefaultApplication('675866902530372', '5650b9940ca593edf25a6b799308d233');
        $helper = new FacebookRedirectLoginHelper();
        $this->session = $helper->getSessionFromRedirect();
        $request = new FacebookRequest($session, 'GET', '\me');
        $response = $request->execute();
        $this->graphObject = $response->getGraphObject();
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