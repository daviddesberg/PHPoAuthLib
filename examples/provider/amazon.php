<?php

/**
 * Example of retrieving an authentication token of the Amazon service.
 *
 * PHP version 5.4
 *
 * @author     Flávio Heleno <flaviohbatista@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Storage\Session;
use OAuth\Helper\Example;
use OAuth\OAuth2\Service\Amazon;

require_once __DIR__ . '/../bootstrap.php';

$helper = new Example();
$storage = new Session();
$client = new CurlClient();

$helper->setTitle('Amazon');
if (empty($_GET)) {
    echo $helper->getContent();
} elseif (!empty($_GET['key']) && !empty($_GET['secret']) && $_GET['oauth'] !== 'redirect') {
    $credentials = new Credentials($_GET['key'], $_GET['secret'], $helper->getCurrentUrl());
    $service = new Amazon($credentials, $client, $storage);
    echo $helper->getHeader();
    echo '<a href="' . $service->getAuthorizationUri() . '">get access token</a>';
    echo $helper->getFooter();
} elseif (!empty($_GET['code'])) {
    $credentials = new Credentials($_GET['key'], $_GET['secret'], $helper->getCurrentUrl());
    $service = new Amazon($credentials, $client, $storage);

    echo $helper->getHeader();

    try {
        $token = $service->requestAccessToken($_GET['code']);
        echo 'access token: ' . $token->getAccessToken();
    } catch (TokenResponseException $exception) {
        $helper->getErrorMessage($exception);
    }
    echo $helper->getFooter();
}
