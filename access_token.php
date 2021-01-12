<?php

require_once('credential.php');
require_once('TwitterAPIExchange.php');

$settings = array(
    'oauth_access_token' => $oauthAccessToken,
    'oauth_access_token_secret' => $oauthAccessTokenSecret,
    'consumer_key' => $apiKey,
    'consumer_secret' => $apiKeySecret
);
$url = 'https://api.twitter.com/oauth/access_token?oauth_token=nzfvxwAAAAABLmeuAAABdvWdcgk&oauth_verifier=R5AO2fsqzqrJjF5sl13jS4ile5i8QyAr';
$requestMethod = 'POST';
$postfield = array(
	'oauth_token' => 'nzfvxwAAAAABLmeuAAABdvWdcgk',
	'oauth_verifier' => 'R5AO2fsqzqrJjF5sl13jS4ile5i8QyAr'
);
$twitter = new TwitterAPIExchange($settings);
$response = $twitter->buildOauth($url, $requestMethod)
	->setPostfields($postfield)
    ->performRequest();
echo $response;
