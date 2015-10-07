<?php

/**
 * Example of retrieving an authentication token of the Deezer service
 *
 * @author  Pedro Amorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    http://developers.deezer.com/api/
 */

use OAuth\OAuth2\Service\Deezer;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['deezer']['key'],
    $servicesCredentials['deezer']['secret'],
    $currentUri->getAbsoluteUri() // Deezer require Https callback's url
);
$serviceFactory->setHttpClient(new CurlClient);
// Instantiate the Deezer service using the credentials, http client and storage mechanism for the token
/** @var $deezerService Deezer */
$deezerService = $serviceFactory->createService('deezer', $credentials, $storage, [Deezer::SCOPE_BASIC_ACCESS, Deezer::SCOPE_OFFLINE_ACCESS, Deezer::SCOPE_EMAIL, Deezer::SCOPE_DELETE_LIBRARY]);

if (!empty($_GET['code'])) {
    // retrieve the CSRF state parameter
    $state = isset($_GET['state']) ? $_GET['state'] : null;
    // This was a callback request from deezer, get the token
    $token = $deezerService->requestAccessToken($_GET['code'], $state);
    // Show some of the resultant data
    $result = json_decode($deezerService->request('user/me'), true);
    echo 'Hello ' . ucfirst($result['name'])
    . ' your Deezer Id is ' . $result['id'];
    echo '<br><img src="'.$result['picture_medium'].'">';

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    $url = $deezerService->getAuthorizationUri();
    header('Location: ' . $url);
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Deezer!</a>";
}
