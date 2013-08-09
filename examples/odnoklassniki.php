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

// Set the application (public) key
$appKey = 'MYAPPLICATIONKEY';

// Instantiate the Facebook service using the credentials, http client and storage mechanism for the token
/** @var $facebookService Facebook */
$odnoklassnikiService = $serviceFactory->createService('odnoklassniki', $credentials, $storage, array());

if( !empty( $_GET['code'] ) ) {
    // This was a callback request from google, get the token
    $token = $odnoklassnikiService->requestAccessToken( $_GET['code'] );

	$extraParams = $token->getExtraParams();
	$uid = $extraParams['user_id'];

    // Send a request with it
	$req = $odnoklassnikiService->request("/users/getCurrentUser", 'GET', array(
			'application_key' => $appKey
		));
	$result = json_decode($req, true);

    // Show some of the resultant data
    echo 'Your unique odnoklassniki user id is: ' . $result['uid']
	    . ' and your name is ' . $currentUser['name']
        . ' and your photo is: <br><img src="' . $result['pic_2'] . '">';

} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $odnoklassnikiService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Odnoklassniki!</a>";
}
