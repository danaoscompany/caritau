<?php
$oauthToken = $_GET['oauth_token'];
$oauthVerifier = $_GET['oauth_verifier'];
file_put_contents("oauth_token.txt", $oauthToken);
file_put_contents("oauth_verifier.txt", $oauthVerifier);
