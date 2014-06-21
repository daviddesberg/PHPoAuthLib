<?php

/**
 * Example of retrieving an authentication token of the Vimeo service
 * Copied of the Instagram example.
 *
 * PHP version 5.4
 *
 * @author     Joakim Israelsson <joakim.israelsson.86@gmail.com>
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Vimeo;
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
    $servicesCredentials['vimeo']['key'],
    $servicesCredentials['vimeo']['secret'],
    $currentUri->getAbsoluteUri()
);

$scopes = array(Vimeo::SCOPE_PUBLIC, Vimeo::SCOPE_PRIVATE);

// Instantiate the Vimeo service using the credentials, http client and storage mechanism for the token
/** @var $vimeoService Vimeo */
$vimeoService = $serviceFactory->createService('vimeo', $credentials, $storage, $scopes);

if (!empty($_GET['code'])) {
    // This was a callback request from Vimeo, get the token
    $at = $vimeoService->requestAccessToken($_GET['code']);
    $extra = $at->getExtraParams();
    $id   = explode('/',$extra['user']['uri'])[2];
    $name = $extra['user']['name'];

    // This information is the same as that in the extra parameter
    // 'user' above. We request this data here from the /me endpoint
    // just to provide that it works.
    $result = json_decode($vimeoService->request('/me'), true);
    $accountType = $result['account'];

    // Show some of the resultant data
    echo "Your unique Vimeo user id is $id, your name is $name and you have a $accountType account.";

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $vimeoService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Vimeo!</a>";
}
