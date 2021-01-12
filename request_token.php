<?php

require_once('credential.php');
require_once('TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => $oauthAccessToken,
    'oauth_access_token_secret' => $oauthAccessTokenSecret,
    'consumer_key' => $apiKey,
    'consumer_secret' => $apiKeySecret
);
$url = 'https://api.twitter.com/oauth/request_token';
$requestMethod = 'POST';
$postfield = array(
	'oauth_callback' => 'https%3A%2F%2Faa7e8591745f.ngrok.io%2Fcallback.php'
);
$twitter = new TwitterAPIExchange($settings);
$response = $twitter->buildOauth($url, $requestMethod)
	->setPostfields($postfield)
    ->performRequest();
$oauthToken = explode('=', explode('&', $response)[0])[1];
$oauthTokenSecret = explode('=', explode('&', $response)[1])[1];
echo $oauthToken . "<br/><br/>";
echo $oauthTokenSecret;
