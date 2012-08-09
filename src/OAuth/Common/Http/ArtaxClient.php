<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Http;
use OAuth\Common\Http\Exception\TokenResponseException;

use Artax\Http\StdRequest;
use Artax\Http\Client;

/**
 * Client interface for the Artax HTTP Client
 */
class ArtaxClient implements ClientInterface
{
    /**
     * Any implementing HTTP providers should send a POST request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param UriInterface $endpoint
     * @param mixed $requestBody
     * @param array $extraHeaders
     * @param string $method
     * @return string
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     */
    public function retrieveResponse(UriInterface $endpoint, $requestBody, array $extraHeaders = [], $method = 'POST')
    {
        // Normalize method name
        $method = strtoupper($method);

        // Normalize headers
        array_walk( $extraHeaders,
            function(&$val, &$key)
            {
                $key = ucfirst( strtolower($key) );
            }
        );


        if( $method === 'GET' && !empty($body) ) {
            throw new \InvalidArgumentException('No body expected for "GET" request.');
        }

        if( !isset($extraHeaders['Content-type'] ) && $method === 'POST' & is_array($requestBody) ) {
            $extraHeaders['Content-type'] = 'application/x-www-form-urlencoded';
        }

        if( is_array($requestBody) ) {
            $requestBody = http_build_query($requestBody);
        }

        // Build and send the HTTP request
        $request = new StdRequest( $endpoint->getAbsoluteUri(), $method, $extraHeaders, $requestBody );
        $client = new Client();

        // Retrieve the response
        $response = $client->request($request);
        if( $response->getStatusCode() >= 400 ) {
            throw new TokenResponseException( $response->getStatusCode() . ': ' . $response->getStatusDescription() );
        }

        // Return the body per the interface spec
        return $response->getBody();
    }

}
