<?php

/**
 * Example of retrieving an authentication token of the Geocaching service
 *
 * @author     Surfoo <surfooo@gmail.com>
 * @copyright  Copyright (c) 2015 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Geocaching;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Uri\Uri;

// EndPoints
define('ENDPOINT_STAGING', 'http://staging.geocaching.com/OAuth/oauth.ashx');
define('ENDPOINT_LIVE', 'https://www.geocaching.com/OAuth/oauth.ashx');
define('ENDPOINT_LIVE_MOBILE', 'https://www.geocaching.com/oauth/mobileoauth.ashx');

// BaseApi
define('BASEAPI_STAGING', 'https://staging.api.groundspeak.com/Live/V6Beta/geocaching.svc/');
define('BASEAPI_LIVE', 'https://api.groundspeak.com/LiveV6/geocaching.svc/');

/**
 * Bootstrap the example
 */
require_once __DIR__.'/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
    $servicesCredentials['geocaching']['key'],
    $servicesCredentials['geocaching']['secret'],
    $currentUri->getAbsoluteUri()
);

$baseapi = new Uri(BASEAPI_LIVE);

// Instantiate the Geocaching service using the credentials, http client and storage mechanism for the token
$geocachingService = $serviceFactory->createService('Geocaching', $credentials, $storage, null, $baseapi);
$geocachingService->setEndPoint(ENDPOINT_LIVE);

$step = isset($_GET['step']) ? (int)$_GET['step'] : null;

$oauth_token = isset($_GET['oauth_token']) ? $_GET['oauth_token'] : null;
$oauth_verifier = isset($_GET['oauth_verifier']) ? $_GET['oauth_verifier'] : null;

if($oauth_token && $oauth_verifier){
    $step = 2;
}

switch($step){
    default:
        print "<a href='".$currentUri->getRelativeUri().'?step=1'."'>Login with Geocaching!</a>";
        break;
    
    case 1:
        
        if($token = $geocachingService->requestRequestToken()){
            $oauth_token = $token->getAccessToken();
            $secret = $token->getAccessTokenSecret();
            
            if($oauth_token && $secret){
                $url = $geocachingService->getAuthorizationUri(array('oauth_token' => $oauth_token, 'perms' => 'write'));
                header('Location: '.$url);
            }
        }
        
        break;
    
    case 2:
        $token = $storage->retrieveAccessToken('Geocaching');
        $secret = $token->getAccessTokenSecret();
        
        if($token = $geocachingService->requestAccessToken($oauth_token, $oauth_verifier, $secret)){
            $oauth_token = $token->getAccessToken();
            $secret = $token->getAccessTokenSecret();
            
            $storage->storeAccessToken('Geocaching', $token);
            
            header('Location: '.$currentUri->getAbsoluteUri().'?step=3');
        }
        break;
    
    case 3:
        $xml = simplexml_load_string($geocachingService->request('geocaching.test.login'));
        print "status: ".(string)$xml->attributes()->stat."\n";
        break;
}
