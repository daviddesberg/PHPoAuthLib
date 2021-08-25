<?php

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Storage\Session;
use OAuth\Helper\Example;
use OAuth\OAuth2\Service\Google;

require_once __DIR__ . '/../bootstrap.php';

$helper = new Example();
$storage = new Session();
$client = new CurlClient();
$helper->setTitle('Google');

if (empty($_GET)) {
    echo $helper->getContent();
} elseif (!empty($_GET['key']) && !empty($_GET['secret']) && $_GET['oauth'] !== 'redirect') {
    echo $helper->getHeader();

    try {
        $credentials = new Credentials($_GET['key'], $_GET['secret'], $helper->getCurrentUrl());
        $google = new Google($credentials, $client, $storage, ['email']);
        echo '<a href="' . $google->getAuthorizationUri() . '">get access token</a>';
    } catch (\Exception $exception) {
        $helper->getErrorMessage($exception);
    }
    echo $helper->getFooter();
} elseif (!empty($_GET['code'])) {
    $credentials = new Credentials($_GET['key'], $_GET['secret'], $helper->getCurrentUrl());
    $google = new Google($credentials, $client, $storage);

    echo $helper->getHeader();

    try {
        $token = $google->requestAccessToken($_GET['code']);
        echo 'access token: ' . $token->getAccessToken();
    } catch (TokenResponseException $exception) {
        $helper->getErrorMessage($exception);
    }
    echo $helper->getFooter();
}
