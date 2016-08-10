<?php

/**
 * Example of retrieving an authentication token of the Yandex service
 *
 * PHP version 5.4
 *
 * @author     Andrey Astashov <mvc.aaa@gmail.com>
 * @copyright  Copyright (c) 2016
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Http\Client\CurlClient;
use OAuth\OAuth2\Service\Yandex;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
//$storage = new Redis(new Predis\Client(), 'yandextest', 'yandexstate');
$storage = new Session();


// Setup the credentials for the requests
$credentials = new Credentials(
  $servicesCredentials['yandex']['key'],
  $servicesCredentials['yandex']['secret'],
  $currentUri->getAbsoluteUri()
);

// Instantiate the Yandex service using the credentials, http client and storage mechanism for the token

$serviceFactory->setHttpClient(new CurlClient);
/** @var $yandex Yandex */
$yandex = $serviceFactory->createService('Yandex', $credentials, $storage);

if (!empty($_GET['code'])) {
    // This was a callback request from github, get the token
    $yandex->requestAccessToken($_GET['code']);
    $result = json_decode($yandex->request("/"));
    
    echo 'Got token:  ' . $token->getAccessToken()."<BR>";
    echo 'The user name of your yandex account is ' . $result->real_name."<BR>";
    echo 'The first email on your yandex account is ' . $result->default_email."<BR>";

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $yandex->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Yandex!</a>";
}
