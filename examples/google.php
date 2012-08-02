<?php
use OAuth2\Service\Google;
use OAuth2\Client\Credentials;

// Testing params
define('GOOGLE_KEY', '');
define('GOOGLE_SECRET', '');


require_once __DIR__ . '/../Artax/Artax.php';

// public domain code that i'm using because of how lazy i was when trying to test this
function get_own_url()
{
    $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
    $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), '/').$s;
    $port = ( $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ) ? '' : (':' . $_SERVER['SERVER_PORT'] );
    return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }

spl_autoload_register
(
    function ($class)
    {
        $class = ltrim($class, '\\');
        preg_match('/^(.+)?([^\\\\]+)$/U', $class, $match);
        $class = str_replace('\\', '/', $match[1]) . str_replace(['\\', '_'], '/', $match[2]) . '.php';
        require_once __DIR__ . '/../' .$class;
    }
);


$sessionStore = new OAuth2\DataStore\Session();
$credentials = new Credentials(GOOGLE_KEY, GOOGLE_SECRET, 'http://localhost/temp/oauth/examples/google.php' );
$googleService = new Google($credentials, $sessionStore, [ Google::SCOPE_EMAIL, Google::SCOPE_PROFILE ]);

// This was a callback request from google
if( !empty( $_GET['code'] ) ) {
    $googleService->requestAccessToken( $_GET['code'] );
    $token = $sessionStore->retrieveAccessToken();
    var_dump($token);
} elseif( !empty($_GET['go'] ) && $_GET['go'] == 'go' ) {
    $url = $googleService->getAuthorizationUrl();
    header('Location: ' . $url);
} else {
    $url = get_own_url() . '?go=go';
    echo "<a href='$url'>Login with Google!</a>";
}