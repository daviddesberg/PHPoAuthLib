<?php

/**
 * Example for connecting with Envato
 *
 * @author     Dave Goosens <leansoft@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Envato;
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
    $servicesCredentials['envato']['key'],
    $servicesCredentials['envato']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Envato service using the credentials, http client and storage mechanism for the token

$envatoService = $serviceFactory->createService('envato', $credentials, $storage, array());

if (!empty($_GET['code'])) {
    // This was a callback request from Envato, get the token
    $token = $envatoService->requestAccessToken($_GET['code']);

    // Send a request with it and decode the json response into an object
    $result = json_decode($envatoService->request('/private/user/username.json'));

    // Show the username of the user
    echo "Your Envato username is {$result->username}!";

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $envatoService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Envato!</a>";
}