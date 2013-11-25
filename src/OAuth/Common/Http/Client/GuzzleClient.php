<?php
namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Exception\GuzzleException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Client implementation for Guzzle
 */
class GuzzleClient extends AbstractClient
{
    protected $client;

    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {

        try {
            $request = $this->client()->createRequest($method, $endpoint, $extraHeaders, $requestBody);
            $response = $request->send();
            return $response->getBody();

        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $response = $e->getResponse();
            // See http://docs.guzzlephp.org/en/latest/http-client/response.html

            $http_status = $response->getStatusCode();
            $guzzleException = new GuzzleException("Guzzle HTTP {$http_status} Error");
            $guzzleException->setGuzzleResponse($response);
            throw $guzzleException;
        }
    }

    private function client()
    {
        if (!isset($this->client)) {
            $this->client = new \Guzzle\Http\Client();
        }
        return $this->client;
    }
}

