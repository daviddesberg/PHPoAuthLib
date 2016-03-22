<?php

/**
 * Example of retrieving an authentication token of the Goodreads service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Ethan Clevenger <ethan.c.clevenger@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Goodreads;
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
    $servicesCredentials['goodreads']['key'],
    $servicesCredentials['goodreads']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Goodreads service using the credentials, http client and storage mechanism for the token
/** @var $goodreadsService Goodreads */
$goodreadsService = $serviceFactory->createService('Goodreads', $credentials, $storage);

if (!empty($_GET['oauth_token'])) {
    $token = $storage->retrieveAccessToken('Goodreads');

    // This was a callback request from goodreads, get the token
    // Note that goodreads hasn't implemented oauth_verifier, see ticket: https://www.goodreads.com/topic/show/2043791-missing-oauth-verifier-parameter-on-user-auth-redirect
    // fortunately, sending along nothing doesn't harm anything
    $goodreadsService->requestAccessToken(
        $_GET['oauth_token'],
        '',
        $token->getRequestTokenSecret()
    );

    // Send a request now that we have access token
    // Endpoints are XML
    $result = json_decode(json_encode(simplexml_load_string($goodreadsService->request('api/auth_user'), "SimpleXMLElement", LIBXML_NOCDATA)));

    echo 'result: <pre>' . print_r($result, true) . '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    // extra request needed for oauth1 to request a request token :-)
    $token = $goodreadsService->requestRequestToken();

    $url = $goodreadsService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Goodreads!</a>";
}
