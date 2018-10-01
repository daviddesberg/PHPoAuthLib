<?php

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Storage\Session;

require_once __DIR__ . '/bootstrap.php';


$storage = new Session();

$credentials = new Credentials(
    $servicesCredentials['microsoft_grapht']['CLIENT_ID'],
    $servicesCredentials['microsoft_grapht']['SECRET_CLIENT'],
    $servicesCredentials['microsoft_grapht']['CALLBACK_URL']
);

//Se le asigna el cliente para hacer las peticiones
$serviceFactory->setHttpClient(new CurlClient);
$microsofttGraphtnService = $serviceFactory->createService('microsoftgraph', $credentials, $storage);


if (!empty($_GET['code'])) {
    // Obtiene el token a partir del codigo que se obtiene del callback
    $token = $microsofttGraphtnService->requestAccessToken($_GET['code']);

    // Send a request para obtener las licencias que tiene el tenant
    $result = json_decode($microsofttGraphtnService->request('/subscribedSkus?api-version=1.6'), true);

    // Se muestra el resultado
    echo "<pre>";
    var_dump($result);
    echo "</pre>";

} else {
    $url = $microsofttGraphtnService->getAuthorizationUri();
    header('Location: ' . $url);
}
