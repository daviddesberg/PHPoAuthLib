<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Http
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Common\Http\Client;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Client interface for streams/file_get_contents
 */
class StreamClient implements ClientInterface
{
    private $maxRedirects;
    private $timeout;

    /**
     * @param int $maxRedirects Maximum redirects for client
     * @param int $timeout Request timeout time for client in seconds
     */
    public function __construct($maxRedirects = 5, $timeout = 15)
    {
        $this->maxRedirects = $maxRedirects;
        $this->timeout = $timeout;
    }

    /**
     * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
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
                $val = ucfirst( strtolower($key) ) . ': ' . $val;
            }
        );


        if( $method === 'GET' && !empty($requestBody) ) {
            throw new \InvalidArgumentException('No body expected for "GET" request.');
        }

        if( !isset($extraHeaders['Content-type'] ) && $method === 'POST' && is_array($requestBody) ) {
            $extraHeaders['Content-type'] = 'Content-type: application/x-www-form-urlencoded';
        }

        if( is_array($requestBody) ) {
            $requestBody = http_build_query($requestBody);
        }

        $context = $this->generateStreamContext($requestBody, $extraHeaders, $method);

        $level = error_reporting(0);
        $response = file_get_contents($endpoint->getAbsoluteUri(), 0, $context);
        error_reporting($level);
        if( false === $response ) {
            throw new TokenResponseException( error_get_last()['message'] );
        }

        return $response;
    }

    private function generateStreamContext($body, $headers, $method)
    {
        return stream_context_create([
            'http' => [
                'method'           => $method,
                'header'           => implode("\r\n", $headers),
                'content'          => $body,
                'protocol_version' => '1.1',

                'max_redirects'    => $this->maxRedirects,
                'timeout'          => $this->timeout,
            ],
        ]);
    }
}
