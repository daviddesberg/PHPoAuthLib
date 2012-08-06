<?php
/**
 * Example of retrieving an authentication token of the Github service
 *
 * PHP version 5.4
 *
 * @author     Lusitanian <alusitanian@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
use OAuth\OAuth2\Service\GitHub;
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

// Use the CurlClient http client
$httpClient = new OAuth\Common\Http\CurlClient();

// Instantiate the google service using the credentials, http client and storage mechanism for the token
$gitHub = new GitHub($credentials, $httpClient, $storage, [ GitHub::SCOPE_USER ]);

if( !empty( $_GET['code'] ) ) {
    // This was a callback request from google, get the token
    $gitHub->requestAccessToken( $_GET['code'] );
    $result = json_decode( $gitHub->sendAuthenticatedRequest( new Uri( 'https://api.github.com/user/emails' ), [], 'GET' ), true );
    echo 'The first email on your github account is ' . $result[0];

} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $gitHub->getAuthorizationUri();
    header('Location: ' . $url);

} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Github!</a>";
}