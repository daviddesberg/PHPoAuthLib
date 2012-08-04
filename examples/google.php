<?php
/**
 * Example of retrieving an authentication token of the Google service
 *
 * PHP version 5.4
 *
 * @author     Lusitanian <alusitanian@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
use OAuth\OAuth2\Service\Google;
use OAuth\Common\Storage\Null;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

$storage = new Null();
$credentials = new Credentials('xxx', 'xxx', $currentUri->getAbsoluteUri());
$httpClient = new OAuth\Common\Http\StreamClient();
$googleService = new Google($credentials, $httpClient, $storage, [ Google::SCOPE_EMAIL, Google::SCOPE_PROFILE ]);

if( !empty( $_GET['code'] ) ) {
    // This was a callback request from google, get and display the token
    $token = $googleService->requestAccessToken( $_GET['code'] );
    var_dump($token);
} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $googleService->getAuthorizationUrl();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Google!</a>";
}