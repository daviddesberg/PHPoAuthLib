<?php

/**
 * Example of retrieving an authentication token of the Docusign service
 *
 * PHP version 5.4
 *
 * @author     Naveen Gopala <naveen.gopala@docusign.com>
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Docusign;
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
    $servicesCredentials['docusign']['key'],
    $servicesCredentials['docusign']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Docusign service using the credentials, http client, storage mechanism for the token and profile scope
/** @var $docusignService Docusign */
$docusignService = $serviceFactory->createService('docusign', $credentials, $storage, array('signature'));

if (!empty($_GET['code'])) {
    // This was a callback request from Docusign, get the token
    $token = $docusignService->requestAccessToken($_GET['code']);

    // Send a request with it
    $result = json_decode($docusignService->request('/oauth/userinfo'), true);

    // Show some of the resultant data
    echo 'Your unique Docusign user id is: ' . $result['accounts'][0]['account_id'] . ' and your name is ' . $result['name'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $docusignService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Docusign!</a>";
}