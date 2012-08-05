<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */
namespace OAuth\Common\Http;
use OAuth\Common\Http\Exception\TokenResponseException;

/**
 * Client interface for file_get_contents
 */
class StreamClient implements ClientInterface
{
    /**
     * Any implementing HTTP providers should send a POST request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param UriInterface $endpoint
     * @param array $params
     * @param array $extraHeaders
     * @param string $method
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     * @return string
     */
    public function retrieveResponse(UriInterface $endpoint, array $params, array $extraHeaders = [], $method = 'POST')
    {
        $method = strtoupper($method);

        if( $method === 'GET' && !empty($params) ) {
            throw new \InvalidArgumentException('No body parameters expected for "GET" request.');
        }

        // Normalize headers
        array_walk( $extraHeaders,
            function(&$val, &$key)
            {
                $key = ucfirst( strtolower($key) );
            }
        );

        // Build the request headers
        if( !isset($extraHeaders['host'] ) ) {
            $headerArray = ['Host: ' . $endpoint->getHost()];
        }

        // Content-type
        $headerArray[] = 'Content-type: application/x-www-form-urlencoded';

        foreach($extraHeaders as $name => $val)
        {
            $headerArray[] = "$name: $val";
        }

        // Build the request body and stream context
        $requestBody = http_build_query($params);
        $streamContext = stream_context_create( [ 'http' => [ 'method' => $method, 'content' => $requestBody, 'header' => $headerArray ] ] );

        // Get the result and resultant status code from the wonderful magical PHP var $http_response_header
        $result = @file_get_contents($endpoint->getAbsoluteUri(), false, $streamContext); // the "@" operator makes me a sad panda
        $statusCode = intval( explode(' ', $http_response_header[0])[1] );

        if( $statusCode >= 400 ) {
            throw new TokenResponseException('Server returned HTTP response code ' . $statusCode );
        } elseif( empty($result) ) {
            throw new TokenResponseException('file_get_contents returned an unknown error');
        }

        return $result;
    }

}