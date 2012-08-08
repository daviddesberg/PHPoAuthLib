<?php
/**
 * Simple generator to streamline the creation of new service implementations that don't require too many quirks.
 *
 * PHP version 5.4
 *
 * @author     Lusitanian <alusitanian@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * Simple function to get one line of text from the command line
 * @return string
 */
function getLine()
{
    $h = fopen('php://stdin', 'r');
    $line = fgets($h);
    fclose($h);
    return trim( $line );
}

/**
 * olawdwtfisthisidonteven
 * @param $question
 * @param string $default
 * @return bool
 */
function confirm($question, $default = 'y')
{
    $default = strtolower($default);
    echo $question . ' (';
    if( 'y' === $default ) {
        echo 'Y/n';
    } else {
        echo 'y/N';
    }
    echo ') ';

    $response = strtolower( getLine() );
    switch($response)
    {
        case 'y':
        case 'yeah':
        case 'yippy-kay-yay-mutha-fuhka':
            return true;
            break;
        case 'n':
        case 'no':
            return false;
            break;
        default:
            return ( $default === 'y' ) ? true : false;
    }
}

echo 'Would you like to generate an OAuth 1 or 2 service class? (1/2) ';
$version = getLine();
if( 1 == $version ) {
    die('Not currently supported.');
} elseif( 2 == $version ) {
    echo 'What is the name of the service? ';
    $serviceName = ucfirst( strtolower(getLine() ) );

    echo 'What is the authorization endpoint of the service? ';
    $authorizationEndpoint = getLine();

    echo 'What is the token endpoint of the service? ';
    $tokenEndpoint = getLine();

    authmethod: // yeah. i went there. i used a label and a goto. get. at. me.
    echo 'Does the API authorization method use:' . "\n";
    echo "\t 1. an 'Authorization' header beginning with 'OAuth' \n";
    echo "\t 2. an 'Authorization' header beginning with 'Bearer' \n";
    echo "\t 3. a query string parameter? \n";

    $authMethod = intval( getLine() ) - 1;
    if( $authMethod < 0 || $authMethod > 2 ) {
        echo "Invalid authorization method specified \n\n";
        goto authmethod; /* http://xkcd.com/292/ --- raptors have attacked me */
    }

    $authConstant = 'AUTHORIZATION_METHOD_HEADER_OAUTH';

    if( $authMethod === 1 ) {
        $authConstant = 'AUTHORIZATION_METHOD_HEADER_BEARER';
    } elseif( $authMethod === 2 ) {
        $authConstant = 'AUTHORIZATION_METHOD_QUERY_STRING';
    }

    echo "Please enter a scope name followed by a : and then the value (i.e. publicrepoaccess:public_repo). Enter '_quit_' when you are done.\n";
    $scopeCode = '';

    while( ( $scope = getLine() ) !== '_quit_' ) {
        $parts = explode(':', $scope);
        $name = trim( $parts[0] );
        $val = '\'' . addslashes( trim( $parts[1] ) ) . '\'';
        $scopeCode .= "\t";
        $scopeCode .= 'const SCOPE_' . strtoupper($name) . ' = ' . $val . ';' . "\n";
    }

    $continue = confirm("Are you sure you would like to generate a class for OAuth2 service $serviceName?");

    if( !$continue ) {
        die("\nClass not generated.\n");
    }

    echo "\n\nGenerating...";
    $tpl = file_get_contents('oauth2tpl.txt');
    $tpl = str_replace('!!service!!', $serviceName, $tpl);
    $tpl = str_replace('!!scopes!!', $scopeCode, $tpl);
    $tpl = str_replace('!!auth_ep!!', $authorizationEndpoint, $tpl);
    $tpl = str_replace('!!tok_ep!!', $tokenEndpoint, $tpl);
    $tpl = str_replace('!!auth_method_constant_name!!', $authConstant, $tpl);

    $filename = __DIR__  . '/../src/OAuth/OAuth2/Service/' . $serviceName . '.php';

    file_put_contents($filename, $tpl);

    echo "\nService generated and saved to $filename";
    echo "\n\n";
} else {
    die('Unknown version requested.');
}

