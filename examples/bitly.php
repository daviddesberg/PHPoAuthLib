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
use OAuth\OAuth2\Service\Bitly;
use OAuth\Common\Storage\Memory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// In-memory storage
$storage = new Memory();

// Replace 'xxx' with your client id and 'yyy' with your secret
$credentials = new Credentials('xxx', 'yyy', $currentUri->getAbsoluteUri());

// Use the GuzzleClient http client (requires the Guzzle phar)
$httpClient = new OAuth\Common\Http\CurlClient();

// Instantiate the google service using the credentials, http client and storage mechanism for the token
$bitlyService = new Bitly($credentials, $httpClient, $storage, []);

if( !empty( $_GET['code'] ) ) {
    // This was a callback request from bitly, get the token
    $bitlyService->requestAccessToken( $_GET['code'] );

    // Send a request with it
    $result = json_decode( $bitlyService->sendAuthenticatedRequest( new Uri('https://api-ssl.bitly.com/v3/user/info'), [], 'GET' ), true );

    // Show some of the resultant data
    echo 'Your unique google user id is: ' . $result['data']['login'] . ' and your name is ' . $result['data']['display_name'];

} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $bitlyService->getAuthorizationUrl();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Bitly!</a>";
}