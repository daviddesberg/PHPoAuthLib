<?php

/**
 * Example of retrieving an authentication token from the JawboneUP service.
 *
 * PHP version 5.4
 *
 * @author     Andrii Gakhov <andrii.gakhov@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\JawboneUP;

/**
 * Bootstrap the example.
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['jawbone']['key'],
    $servicesCredentials['jawbone']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Jawbone UP service using the credentials, http client and storage mechanism for the token
/** @var JawboneUP $jawboneService */
$jawboneService = $serviceFactory->createService('JawboneUP', $credentials, $storage, []);

if (!empty($_GET['code'])) {
    // This was a callback request from JawboneUP, get the token
    $token = $jawboneService->requestAccessToken($_GET['code']);

    // Send a request with it
    $result = json_decode($jawboneService->request('/users/@me'), true);

    // Show some of the resultant data
    echo 'Your unique Jawbone UP user id is: ' . $result['data']['xid'];
} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $jawboneService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Jawbone UP!</a>";
}
