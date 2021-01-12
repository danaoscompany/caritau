<?php

require_once('credential.php');
require_once('TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => $oauthAccessToken,
    'oauth_access_token_secret' => $oauthAccessTokenSecret,
    'consumer_key' => $apiKey,
    'consumer_secret' => $apiKeySecret
);
$url = 'https://api.twitter.com/1.1/trends/place.json';
$getfield = '?id=1047378';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();
