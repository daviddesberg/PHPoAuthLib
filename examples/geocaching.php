<?php

/**
 * Example of retrieving an authentication token of the Geocaching service
 *
 * @author     Surfoo <surfooo@gmail.com>
 * @copyright  Copyright (c) 2014 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\OAuth1\Service\Geocaching;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;

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

$curlClient = new CurlClient;
$curlClient->setForceSSL3(true);

$serviceFactory->setHttpClient($curlClient);

// Instantiate the Geocaching service using the credentials, http client and storage mechanism for the token
$geocachingService = $serviceFactory->createService('Geocaching', $credentials, $storage);

// These 2 lines are useless for production
$geocachingService->setBaseApiUri('staging');
$geocachingService->setEndPoint('staging');

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
        $deviceInfo['ApplicationCurrentMemoryUsage'] = 2048*1024;
        $deviceInfo['ApplicationPeakMemoryUsage']    = 2048*1024;
        $deviceInfo['ApplicationSoftwareVersion']    = 'blabla';
        $deviceInfo['DeviceManufacturer']            = 'blabla';
        $deviceInfo['DeviceName']                    = 'blabla';
        $deviceInfo['DeviceOperatingSystem']         = 'blabla';
        $deviceInfo['DeviceTotalMemoryInMB']         = 2048*1024;
        $deviceInfo['DeviceUniqueId']                = 'blabla';
        $deviceInfo['MobileHardwareVersion']         = 'blabla';
        $deviceInfo['WebBrowserVersion']             = 'blabla';

        $params = array('ProfileOptions' => array('PublicProfileData' => true), 'DeviceInfo' => $deviceInfo);

        $user = $geocachingService->request('GetYourUserProfile', 'POST', $params);

        echo "<p>Logged as: <strong>".$user->Profile->User->UserName."</strong><br />";
        preg_match('/([0-9]+)/', $user->Profile->PublicProfile->MemberSince, $matches);
        $memberSince = date('Y-m-d H:i:s', floor($matches[0]/1000));
        echo "Member since: <strong>" . $memberSince . "</strong></p>\n";
        break;
}
