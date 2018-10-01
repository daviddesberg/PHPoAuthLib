<?php

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;

require_once __DIR__ . '/bootstrap.php';


$storage = new Session();

$credentials = new Credentials(
    $servicesCredentials['microsoft_grapht']['CLIENT_ID'],
    $servicesCredentials['microsoft_grapht']['SECRET_CLIENT'],
    $servicesCredentials['microsoft_grapht']['CALLBACK_URL']
);
var_dump($credentials);