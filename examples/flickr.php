<?php

/**
 * Example of retrieving an authentication token of the Flickr service
 *
 * @author     Christian Mayer <thefox21at@gmail.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Flickr;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;

/**
 * Bootstrap the example
 */
require_once __DIR__.'/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials['flickr']['key'],
	$servicesCredentials['flickr']['secret'],
	$currentUri->getAbsoluteUri()
);

// Instantiate the Flickr service using the credentials, http client and storage mechanism for the token
/** @var $flickr Flickr */
$serviceFactory->setHttpClient(new CurlClient());
$flickrService = $serviceFactory->createService('Flickr', $credentials, $storage, array());

/*
if (!empty($_GET['code'])){
	// This was a callback request from Flickr, get the token
	$flickr->requestAccessToken($_GET['code']);
	
	$result = json_decode($flickr->request('flickr.test.login'), true);
}
elseif(!empty($_GET['go']) && $_GET['go'] === 'go'){
	$url = $flickr->getAuthorizationUri();
	header('Location: '.$url);
}
else{
	$url = $currentUri->getRelativeUri().'?go=go';
	echo "<a href='$url'>Login with Flickr!</a>";
}
*/

$step = isset($_GET['step']) ? (int)$_GET['step'] : null;
switch($step){
	case 1:
		
		if($token = $flickrService->requestRequestToken()){
			$accessToken = $token->getAccessToken();
			$accessTokenSecret = $token->getAccessTokenSecret();
			
			if($accessToken && $accessTokenSecret){
				$url = $flickrService->getAuthorizationUri(array('oauth_token' => $accessToken, 'perms' => 'write'));
				print "url: '$url'";
				#header('Location: '.$url);
			}
		}
		
		break;
	
	default:
		print "<a href='".$currentUri->getRelativeUri().'?step=1'."'>Login with Flickr!</a>";
		break;
}
