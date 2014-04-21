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
    protected $lastResponse;

    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {

        try {
            $request = $this->client()->createRequest($method, $endpoint, $extraHeaders, $requestBody);
            $this->lastResponse = $request->send();
            return $this->lastResponse->getBody();

        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $this->lastResponse = $e->getResponse();
            // See http://docs.guzzlephp.org/en/latest/http-client/response.html

            $http_status = $this->lastResponse->getStatusCode();
            $guzzleException = new GuzzleException("Guzzle HTTP {$http_status} Error");
            $guzzleException->setGuzzleResponse($this->lastResponse);
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

    public function getLastResponse()
    {
        return $this->lastResponse;
    }
}
