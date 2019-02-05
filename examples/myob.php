<?php
/**
 * Example of retrieving an authentication token of the MYOB service
 *
 * PHP version 5.4
 *
 * @author     Jervy Escoto <jervy.escoto@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

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
    $servicesCredentials['myob']['key'],
    $servicesCredentials['myob']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate MYOB service using the credentials, http client, storage mechanism for the token and profile scope
/** @var OAuth\OAuth2\Service\Myob $myobService */
$myobService = $serviceFactory->createService('myob', $credentials, $storage,
    array(\OAuth\OAuth2\Service\Myob::SCOPE_COMPANYFILE));

if (!empty($_GET['code'])) {
    // This was a callback request from MYOB, get the token
    $token = $myobService->requestAccessToken($_GET['code']);

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $myobService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with MYOB!</a>";
}
