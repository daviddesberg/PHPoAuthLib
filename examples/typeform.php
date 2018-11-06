<?php
/**
 * Example of retrieving an authentication token of the Typeform service
 *
 * PHP version 5.4
 *
 * @author     Yaacov Cohen <yaacov@goodimpact.studio>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
use OAuth\OAuth2\Service\Typeform;
use OAuth\Common\Storage\Session;
use OAuth\Common\Consumer\Credentials;

/**
 * Bootstrap the example
 */
require_once __DIR__ . '/bootstrap.php';

// Session storage
$storage = new Session();

// Setup the credentials for the requests
$credentials = new Credentials(
	$servicesCredentials['typeform']['key'],
	$servicesCredentials['typeform']['secret'],
	$currentUri->getAbsoluteUri()
);

$form_id = $servicesCredentials['form_id'];

// Instantiate the Typeform service using the credentials, http client and storage mechanism for the token
/** @var $typeformService Typeform */
$typeformService = $serviceFactory->createService('typeform', $credentials, $storage);
if (!empty($_GET['code'])) {

	// This was a callback request from Typeform, get the token
	$typeformService->requestAccessToken($_GET['code']);

	// Send a request with it
	$result = json_decode($typeformService->request("/forms/$form_id/responses"), true);

	// Show some of the resultant data
	echo 'Total items: ' . $result['total_items'];

} elseif (!empty($_GET['go']) && $_GET['go'] === 'go') {

	$url = $typeformService->getAuthorizationUri();
	header('Location: ' . $url);
} else {

	$url = $currentUri->getRelativeUri() . '?go=go';
	echo "<a href='$url'>Login with Typeform!</a>";
}
