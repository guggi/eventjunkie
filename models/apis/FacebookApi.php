<?php

namespace app\models\apis;

use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Faker\Provider\DateTime;
use Yii;

class FacebookApi {
    private $url;
    private $session;
    private $images = [];
    private $comments = [];
    private $limit = 1;

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
        $until = strtotime('+1 days');
        $since = strtotime('-9 days');

        $request = new FacebookRequest(
            $this->session,
            'GET',
            '/' . $id . '/feed?since=' . $since . '&until=' . $until . $feed_url
        );

        $json = $request->execute()->getRawResponse();
        $response = json_decode($json);

        $data = $response->{'data'};

        foreach($data as $commentData) {
            if (isset($commentData->{"message"})) { // wegen Exceptions
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
                        'answers' => $this->getAnswers($commentData),
                    ]);
            }
        }

        /*if (isset($response->{'paging'})) {
            $paging = $response->{'paging'};

            if (++$counter < $this->limit) {
                $this->getComment('&' . strpos($paging['next'], 'until'), $counter);
            }
        }*/
    }

    private function getAnswers($commentData) {

        $answers = [];
        if (isset($commentData->{'comments'})) {


            $data = $commentData->{'comments'}->{'data'};

            foreach($data as $answerData) {
                if (isset($answerData->{"message"})) { // wegen Exceptions
                    array_push($answers,
                        [
                            'date' => strtotime($answerData->{"created_time"}),
                            'author' => $answerData->{'from'}->{'name'},
                            'text' => $this->parseMessage($answerData->{"message"}),
                            'author_url' => 'https://www.facebook.com/' . $answerData->{'from'}->{'id'},
                        ]);
                }
            }
        }

        return $answers;
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
