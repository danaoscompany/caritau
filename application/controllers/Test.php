<?php

require_once('TwitterAPIExchange.php');

class Test extends CI_Controller {
	
	private function get_api_key() {
		return "IeoDqJqiXdtltMqmL1nCRNTpY";
	}
	
	private function get_api_secret_key() {
		return "4ezoeQl4LuqM5kSTIiy2MGzGSTBW5nzNuwSDf7vzgqdsBeBoQ3";
	}
	
	private function get_oauth_access_token() {
		return "1348868965552975872-KLe2kIBizw7CMSMvCqz4ZoZgzDdsB4";
	}
	
	private function get_oauth_access_token_secret() {
		return "1UERN0Y5CKFE8Hve2FVfBM9zuzQ9wPekP3AOJg7TwVBNi";
	}
	
	public function request_token() {
		$settings = array(
    		'oauth_access_token' => $this->get_oauth_access_token(),
    		'oauth_access_token_secret' => $this->get_oauth_access_token_secret(),
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_secret_key()
		);
		$url = 'https://api.twitter.com/oauth/request_token';
		$requestMethod = 'POST';
		$postfield = array(
			'oauth_callback' => 'https%3A%2F%2F97df841473d2.ngrok.io%2Fcaritau%2Fuser%2Fcallback'
		);
		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->buildOauth($url, $requestMethod)
			->setPostfields($postfield)
		    ->performRequest();
		$oauthToken = explode('=', explode('&', $response)[0])[1];
		$oauthTokenSecret = explode('=', explode('&', $response)[1])[1];
		echo $response;
	}
	
	public function access_token() {
		$settings = array(
    		'oauth_access_token' => $this->get_oauth_access_token(),
    		'oauth_access_token_secret' => $this->get_oauth_access_token_secret(),
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_secret_key()
		);
		$oauthToken = "_SJBLAAAAAABLmeuAAABdvZFOak";
		$oauthVerifier = "vYpGk2vaoZIiXyYyeoxHg092uM4X1P2e";
		$url = "https://api.twitter.com/oauth/access_token?oauth_token=" . $oauthToken . "&oauth_verifier=" . $oauthVerifier;
		$requestMethod = 'POST';
		$postfield = array(
			'oauth_consumer_key' => $this->get_api_key(),
			'oauth_token' => $oauthToken,
			'oauth_verifier' => $oauthVerifier
		);
		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->buildOauth($url, $requestMethod)
			->setPostfields($postfield)
    		->performRequest();
    	echo $response;
	}
	
	public function trending_topics() {
		$settings = array(
    		'oauth_access_token' => $this->get_oauth_access_token(),
    		'oauth_access_token_secret' => $this->get_oauth_access_token_secret(),
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_secret_key()
		);
		$url = 'https://api.twitter.com/1.1/trends/place.json';
		$getfield = '?id=1047378';
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		echo $twitter->setGetfield($getfield)
    		->buildOauth($url, $requestMethod)
    		->performRequest();
	}
}
