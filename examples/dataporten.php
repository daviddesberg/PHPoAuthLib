<?php

/**
 * Example of retrieving an authentication token of the Dataporten service
 *
 * PHP version 5.4
 *
 * @author     Benjamin Bender <bb@codepoet.de>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Jørgen Birkhaug <jorgen.birkhaug@uib.no>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Dataporten;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['dataporten']['key'],
    $servicesCredentials['dataporten']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Dataporten service using the credentials, http client and storage mechanism for the token
/** @var $dataportenService*/
$dataportenService = $serviceFactory->createService('dataporten', $credentials, $storage, array());

if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter
    $state = isset($_GET['state']) ? $_GET['state'] : null;

    // This was a callback request from Dataporten, get the token and state
    $token = $dataportenService->requestAccessToken($_GET['code'], $state);

    // Send a request with it
    $result = json_decode($dataportenService->request('/userinfo'), true);

    echo '<pre>';
    // See: https://docs.dataporten.no/docs/oauth-authentication/
    print_r($result);
    echo '</pre>';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $dataportenService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Dataporten.</a>";
}