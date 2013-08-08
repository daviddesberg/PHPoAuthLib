<?php
/**
 * Example of retrieving an authentication token of the Facebook service
 *
 * PHP version 5.4
 *
 * @author     Benjamin Bender <bb@codepoet.de>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
use OAuth\OAuth2\Service\Facebook;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\Uri;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['vkontakte']['key'],
    $servicesCredentials['vkontakte']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
/** @var $facebookService Facebook */
$vkontakteService = $serviceFactory->createService('vkontakte', $credentials, $storage, array());

if( !empty( $_GET['code'] ) ) {
    // This was a callback request from google, get the token
    $token = $vkontakteService->requestAccessToken( $_GET['code'] );

	$extraParams = $token->getExtraParams();
	$uid = $extraParams['user_id'];

    // Send a request with it
	$result = json_decode( $vkontakteService->request("users.get?uids={$uid}&fields=nickname,photo"), true);
	$currentUser = $result['response'][0];

    // Show some of the resultant data
    echo 'Your unique vkontakte user id is: ' . $currentUser['uid']
	    . ' and your name is ' . $currentUser['first_name'] . ' ' . $currentUser['last_name']
        . ' and your photo is: <br><img src="' . $currentUser['photo'] . '">';

} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $vkontakteService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Facebook!</a>";
}
