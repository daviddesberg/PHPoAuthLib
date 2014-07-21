<?php

/**
 * Example of retrieving an authentication token of the FitBit service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\FitBit;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['withings']['key'],
    $servicesCredentials['withings']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Withings service using the credentials, http client and storage mechanism for the token
/** @var $withingsService Withings */
$withingsService = $serviceFactory->createService('Withings', $credentials, $storage);

if (!empty($_GET['oauth_token'])) {
    $token = $storage->retrieveAccessToken('Withings');
    //print_r  ( $token ) ;

    // This was a callback request from withings, get the token
    $withingsService->requestAccessToken(
        $_GET['oauth_token'],
        $_GET['oauth_verifier'],
        $token->getRequestTokenSecret()
    );
    
    // Send a request now that we have access token
    $result = json_decode($withingsService->request( '?userid=' . $_GET['userid'] ) ); // where userid is the user id selected at withings granting access step

    echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    // extra request needed for oauth1 to request a request token :-)
    $token = $withingsService->requestRequestToken();

    $url = $withingsService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Withings!</a>";
}