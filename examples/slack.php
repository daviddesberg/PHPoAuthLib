<?php

/**
 * Example of retrieving an authentication token of the Slack service
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Antoine Corcy <contact@sbin.dk>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth2\Service\Slack;
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
    $servicesCredentials['slack']['key'],
    $servicesCredentials['slack']['secret'],
    $currentUri->getAbsoluteUri()
);

// Instantiate the Slack service using the credentials, http client and storage mechanism for the token
/** @var $slackService Slack */
$slackService = $serviceFactory->createService('slack', $credentials, $storage, array('commands'));

if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter
    $state = isset($_GET['state']) ? $_GET['state'] : null;

    // This was a callback request from slack, get the token
    $token = $slackService->requestAccessToken($_GET['code'], $state);

    // Send a request with it. Please note that XML is the default format.
    $result = json_decode($slackService->request('/api.test'), true);
    var_dump($result);

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $slackService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Slack!</a>";
}
