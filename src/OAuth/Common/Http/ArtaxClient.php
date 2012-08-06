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
     * @param array $params
     * @param array $extraHeaders
     * @param string $method
     * @return string
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     */
    public function retrieveResponse(UriInterface $endpoint, array $params, array $extraHeaders = [], $method = 'POST')
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


        if( $method === 'GET' && !empty($params) ) {
            throw new \InvalidArgumentException('No body parameters expected for "GET" request.');
        }

        $requestBody = http_build_query($params);

        if( !isset( $extraHeaders['Content-length'] ) ) {
            $extraHeaders['Content-length'] = strlen( $requestBody );
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
