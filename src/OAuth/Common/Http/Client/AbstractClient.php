<?php

namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Abstract HTTP client
 */
abstract class AbstractClient implements ClientInterface
{
    protected $maxRedirects = 5;
    protected $timeout      = 15;

    /**
     * @param int $maxRedirects Maximum redirects for client
     *
     * @return ClientInterface
     */
    public function setMaxRedirects($redirects)
    {
        $this->maxRedirects = $redirects;

        return $this;
    }

    /**
     * @param int $timeout Request timeout time for client in seconds
     *
     * @return ClientInterface
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @param array $headers
     */
    public function normalizeHeaders(&$headers)
    {
        // Normalize headers
        array_walk(
            $headers,
            function (&$val, &$key) {
                $key = ucfirst(strtolower($key));
                $val = ucfirst(strtolower($key)) . ': ' . $val;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {
        // Normalize method name
        $method = strtoupper($method);

        $this->normalizeHeaders($extraHeaders);

        if ($method === 'GET' && !empty($requestBody)) {
            if (!is_array($requestBody) && !$requestBody instanceof \Traversable) {
                throw new \InvalidArgumentException('Only array body expected for "GET" request.');
            }

            foreach ($requestBody as $key => $value) {
                $endpoint->addToQuery($key, $value);
            }
        }

        if (!isset($extraHeaders['Content-type']) && $method === 'POST' && is_array($requestBody)) {
            $extraHeaders['Content-type'] = 'Content-type: application/x-www-form-urlencoded';
        }

        $extraHeaders['Host']       = 'Host: '.$endpoint->getHost();
        $extraHeaders['Connection'] = 'Connection: close';

        return $this->doRetrieveResponse($endpoint, $requestBody, $extraHeaders, $method);
    }

    /**
     * Do the real request.
     * @see ClientInterface::retrieveResponse
     *
     * @param UriInterface $endpoint
     * @param mixed        $requestBody
     * @param array        $extraHeaders
     * @param string       $method
     *
     * @return string
     *
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     */
    abstract protected function doRetrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    );
}
