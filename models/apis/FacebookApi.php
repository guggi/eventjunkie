<?php

namespace app\models\apis;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Yii;
use yii\base\Exception;

class FacebookApi {
    private $url;
    private $session;
    private $images = [];
    private $comments = [];

    function __construct($url) {
        $this->url = $url;

        FacebookSession::setDefaultApplication(Yii::$app->params['facebookApiKey'], Yii::$app->params['facebookApiSecret']);

        $data = file_get_contents('https://graph.facebook.com/oauth/access_token?' .
            'client_id=' . Yii::$app->params['facebookApiKey'] . '&client_secret=' . Yii::$app->params['facebookApiSecret'] .
            '&grant_type=client_credentials');

        $accessToken = substr($data, strlen('access_token='));

        $this->session = new FacebookSession($accessToken);
    }

    private function getId() {
        $id_regex = '/[0-9]{5,}/';
        preg_match($id_regex, $this->url, $match);
        return $match[0];
    }

    private function parseMessage($message) {
        $parsed_message = preg_replace(
            '@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@',
            '<a href="$1">$1</a>',
            $message);
        return $parsed_message;
    }

    public function getComments() {
        $this->getComment();
        return $this->comments;
    }

    private function getComment($feed_url = '', $counter = 0) {
        $id = $this->getId();

        $request = new FacebookRequest(
            $this->session,
            'GET',
            '/' . $id . '/feed' . $feed_url
        );

        $response = $request->execute();
        $graphObject = $response->getGraphObject();

        $data = $graphObject->getProperty('data');
        $dataArray = $data->asArray();

        foreach($dataArray as $commentData) {
            array_push($this->comments,
                [
                    'title' => $commentData->{'to'}->{'data'}[0]->{'name'},
                    'date' => strtotime($commentData->{"created_time"}),
                    'author' => $commentData->{'from'}->{'name'},
                    'text' => $this->parseMessage($commentData->{"message"}),
                    'site_name' => 'Facebook',
                    'url' => 'https://www.facebook.com/events/' . $id,
                    'author_url' => 'https://www.facebook.com/' . $commentData->{'from'}->{'id'},
                    'socialmedia_url' => 'https://www.facebook.com/events/' . $id,
                ]);
        }
        /*
                $paging= $graphObject->getProperty('paging');
                $pagingArray = $paging->asAry();

                /*if ($counter++ < $this->limit) {
                    $this->getComment('?' . strpos($pagingArray['next'], '__paging_token'), $counter);
                }*/
    }

    public function getImages() {
        $request = new FacebookRequest(
            $this->session,
            'GET',
            '/' . $this->getId() . '/photos'
        );
        $response = $request->execute();
        $graphObject = $response->getGraphObject();


        $data = $graphObject->getProperty('data');

        $imagesData = $data->asArray();

        foreach($imagesData as $imageData){
            array_push($this->images,
                [
                    'original' => $imageData->{'images'}[0]->{'source'},
                    'thumbnail' => $imageData->{'picture'}
                ]);
        }

        return $this->images;
    }
}