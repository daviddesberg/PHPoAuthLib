<?php

/**
 * Open Bank Project
 * 
 * Example of retrieving an Oauth v1.0 token from Open Bank Project Oauth server
 *
 *
 * @author      Amir Duran <amir.duran@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\OpenBankProject;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\OAuth1\Signature\Signature;
/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage, for testing purposes I choose Session. You can extend TokenStorageInterface and make connection with DB
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['openBankProject']['key'],
    $servicesCredentials['openBankProject']['secret'],
    'http://localhost/oauth/oAuthPHPLibrary/examples/openbankapi.php'
);
$openBankProjectService = new OpenBankProject($credentials, new \OAuth\Common\Http\Client\CurlClient(), $storage, new Signature($credentials));

if (!empty($_GET['oauth_token'])) {
    var_dump($_SESSION);
    $token = $storage->retrieveAccessToken('OpenBankProject');
    // Get access token
    $openBankProjectService->requestAccessToken($_GET['oauth_token'],$_GET['oauth_verifier'],$token->getRequestTokenSecret());
    
    var_dump(json_decode($openBankProjectService->request('https://apisandbox.openbankproject.com/obp/v1.2.1/banks'), true));//Call some standard API
    exit;

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {
    // Obtain request token
    $token = $openBankProjectService->requestRequestToken();
    $url = $openBankProjectService->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
    sleep(5);//Session sometimes remains empty if you don't wait few secs
    header('Location: ' . $url);//Redirect to the Authentification server
} else {
    $url = $currentUri->getRelativeUri() . '?go=go';
    echo "<a href='$url'>Login with Open Project API!</a>";
}