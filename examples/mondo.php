<?php

/**
 * Example of retrieving an authentication token of the Mondo finance API
 *
 * PHP version 5.6
 *
 * @author     Liam Gladdy <liam@gladdy.co.uk>
 * @copyright  Copyright (c) 2016 The author
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Mondo;
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
    $servicesCredentials['mondo']['key'],
    $servicesCredentials['mondo']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Mondo service using the credentials, http client and storage mechanism for the token
/** @var $mondoService Mondo */
$mondoService = $serviceFactory->createService('mondo', $credentials, $storage);

if (!empty($_GET['code'])) {
    // This was a callback request from Mondo, get the token
    $mondoService->requestAccessToken($_GET['code']);

    // Send a request with it
    $result = json_decode($mondoService->request('ping/whoami'), true);
    
    echo 'Your unique user id is: ' . $result['user_id'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $mondoService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Mondo!</a>";
}
