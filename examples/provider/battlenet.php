<?php

/** ---------------------------------------------------------------------------
 * Example of using the Battle.net service.
 *
 * PHP version 5.4
 *
 * @author     Mukunda Johnson (mukunda.com)
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\BattleNet;

/** ---------------------------------------------------------------------------
 * Bootstrap the example.
 */
require_once __DIR__ . '/bootstrap.php';

if (empty($_GET['code']) && !isset($_GET['go'])) {

    // Empty query; show the startup page.

    echo '
    
    <p>Sign-in using Battle.net. Please pick your region:</p>
    
    <p>
        <a href="?go&region=us">USA</a> 
        <a href="?go&region=eu">Europe</a> 
        <a href="?go&region=kr">Korea</a> 
        <a href="?go&region=tw">Taiwan</a> 
        <a href="?go&region=cn">China</a>
    </p>
    
    ';

    die();
}

//////////////////////////////////////////////////////////////////////////////
// Authorization and making a request:
///////////////////////////////////////////////////////////////////////////////

// Session storage
$storage = new Session();

// Set up the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['battlenet']['key'],
    $servicesCredentials['battlenet']['secret'],
    $currentUri->getAbsoluteUri()
);

$region = $_GET['region'] ?? '';

$region_map = [
    'us' => BattleNet::API_URI_US, // USA - this is the default if you omit the base API URI.
    'eu' => BattleNet::API_URI_EU, // Europe
    'kr' => BattleNet::API_URI_KR, // Korea
    'tw' => BattleNet::API_URI_TW, // Taiwan
    'cn' => BattleNet::API_URI_CN, // China
];

// Get base API URI from region.
$apiuri = isset($region_map[$region]) ? new Uri($region_map[$region]) : null;

// Without any scopes, we can get their BattleTag.
$scopes = [];

$battlenetService = $serviceFactory->createService(
                        'battlenet', $credentials, $storage, $scopes, $apiuri);

if (!empty($_GET['code'])) {
    // This was a callback request from Battle.net, get the token
    $token = $battlenetService->requestAccessToken($_GET['code']);

    // See https://dev.battle.net/io-docs for OAuth request types.
    //
    // Without any scopes specified, we can get their BattleTag.
    $result = json_decode($battlenetService->request('/account/user'));

    echo "Your BattleTag is \"$result->battletag\".";
} elseif (isset($_GET['go'])) {
    $url = $battlenetService->getAuthorizationUri();
    header("Location: $url");
}
