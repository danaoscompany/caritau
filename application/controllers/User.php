<?php

require_once('TwitterAPIExchange.php');

class User extends CI_Controller {
	
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
	
	public function update_fcm_id() {
		$androidID = $this->input->post('android_id');
		$fcmID = $this->input->post('fcm_id');
		$userCount = $this->db->query("SELECT * FROM `users` WHERE `android_id`='" . $androidID . "'")->num_rows();
		if ($userCount > 0) {
			$this->db->where('android_id', $androidID);
			$this->db->update('users', array(
				'fcm_id' => $fcmID
			));
		} else {
			$this->db->insert('users', array(
				'android_id' => $androidID,
				'fcm_id' => $fcmID
			));
		}
	}
	
	public function request_token() {
		$androidID = $this->input->post('android_id');
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
		$userCount = $this->db->query("SELECT * FROM `users` WHERE `android_id`='" . $androidID . "'")->num_rows();
		if ($userCount > 0) {
			$this->db->where('android_id', $androidID);
			$this->db->update('users', array(
				'oauth_token' => $oauthToken
			));
		} else {
			$this->db->insert('users', array(
				'android_id' => $androidID,
				'oauth_token' => $oauthToken
			));
		}
		echo $response;
	}
	
	public function trending_topics() {
		$oauthToken = $this->input->post('oauth_token');
		$oauthTokenSecret = $this->input->post('oauth_token_secret');
		$settings = array(
    		'oauth_access_token' => $oauthToken,
    		'oauth_access_token_secret' => $oauthTokenSecret,
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
	
	public function home_timelines() {
		$oauthToken = $this->input->post('oauth_token');
		$oauthTokenSecret = $this->input->post('oauth_token_secret');
		$sinceID = $this->input->post('since_id');
		$settings = array(
    		'oauth_access_token' => $oauthToken,
    		'oauth_access_token_secret' => $oauthTokenSecret,
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_secret_key()
		);
		$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
		$getfield = '?count=10';
		if ($sinceID != "0") {
			$getfield .= '&since_id=' . $sinceID;
		}
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		echo $twitter->setGetfield($getfield)
    		->buildOauth($url, $requestMethod)
    		->performRequest();
	}
	
	public function search() {
		$oauthToken = /*'1348868965552975872-KLe2kIBizw7CMSMvCqz4ZoZgzDdsB4';*/$this->input->post('oauth_token');
		$oauthTokenSecret = /*'1UERN0Y5CKFE8Hve2FVfBM9zuzQ9wPekP3AOJg7TwVBNi';*/$this->input->post('oauth_token_secret');
		$keyword = $this->input->post('keyword');
		$sinceID = $this->input->post('since_id');
		$settings = array(
    		'oauth_access_token' => $oauthToken,
    		'oauth_access_token_secret' => $oauthTokenSecret,
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_secret_key()
		);
		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$getfield = '?q=' . $keyword . '&count=100';
		if ($sinceID != "0") {
			$getfield .= '&since_id=' . $sinceID;
		}
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
		echo $twitter->setGetfield($getfield)
    		->buildOauth($url, $requestMethod)
    		->performRequest();
	}
	
	public function callback() {
		$oauthToken = $this->input->get('oauth_token');
		$oauthVerifier = $this->input->get('oauth_verifier');
		file_put_contents("oauth_token.txt", $oauthToken);
		file_put_contents("oauth_verifier.txt", $oauthVerifier);
		$this->db->where('oauth_token', $oauthToken);
		$this->db->update('users', array(
			'oauth_verifier' => $oauthVerifier
		));
		$prevOAuthToken = $oauthToken;
		$fcmID = $this->db->query("SELECT * FROM `users` WHERE `oauth_token`='" . $oauthToken . "'")->row_array()['fcm_id'];
		
		$settings = array(
    		'oauth_access_token' => $this->get_oauth_access_token(),
    		'oauth_access_token_secret' => $this->get_oauth_access_token_secret(),
    		'consumer_key' => $this->get_api_key(),
    		'consumer_secret' => $this->get_api_key()
		);
		$url = "https://api.twitter.com/oauth/access_token?oauth_token=" . $oauthToken . "&oauth_verifier=" . $oauthVerifier;
		$requestMethod = 'POST';
		$postfield = array(
			'oauth_token' => $oauthToken,
			'oauth_verifier' => $oauthVerifier
		);
		$twitter = new TwitterAPIExchange($settings);
		$response = $twitter->buildOauth($url, $requestMethod)
			->setPostfields($postfield)
    		->performRequest();
    	$oauthToken = explode('=', explode('&', $response)[0])[1];
		$oauthTokenSecret = explode('=', explode('&', $response)[1])[1];
		$this->db->where('oauth_token', $prevOAuthToken);
		$this->db->update('users', array(
			'oauth_token_secret' => $oauthTokenSecret
		));
		
		$url = "https://fcm.googleapis.com/fcm/send";
	    $serverKey = 'AAAAcvkrsQM:APA91bH88DXfduYQPwPSUZBbDTkYYvHrwJvzivwxnVk8Q3Dm9_-5sUu9HQdu9xsEOilRrdLOkgNlidz4SbU21tIrPI8n3hEfuXAXb5m--FBDcnxerw1LQSTpvslZ-a2RbdMLLJ2L8rNf';
	    $title = "Anda sudah terhubung dengan Twitter";
	    $body = "Sekarang Anda dapat melihat tweet terbaru";
	    $notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
	    $arrayToSend = array('to' => $fcmID, 'notification' => $notification,'priority'=>'high', 'data' => array(
	    	'type' => "1",
	    	'oauth_token' => $oauthToken,
	    	'oauth_token_secret' => $oauthTokenSecret,
	    	'oauth_verifier' => $oauthVerifier
	    ));
	    $json = json_encode($arrayToSend);
	    $headers = array();
	    $headers[] = 'Content-Type: application/json';
	    $headers[] = 'Authorization: key='. $serverKey;
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    $response = curl_exec($ch);
	    curl_close($ch);
	    echo "Anda sudah terhubung dengan Twitter. Sekarang Anda bisa kembali ke layar aplikasi Anda untuk mengakses tweet terbaru dan trending topics.";
	}
	
	public function get_latest_token() {
		$androidID = $this->input->post('android_id');
		$user = $this->db->query("SELECT * FROM `users` WHERE `android_id`='" . $androidID . "'")->row_array();
		echo json_encode(array(
			'oauth_token' => $user['oauth_token'],
			'oauth_token_secret' => $user['oauth_token_secret']
		));
	}
}
