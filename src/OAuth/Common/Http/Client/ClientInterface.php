<?php

namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Uri\UriInterface;

/**
 * Any HTTP clients to be used with the library should implement this interface.
 */
interface ClientInterface
{
    /**
     * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param mixed        $requestBody
     * @param string       $method
     *
     * @return string
     */
    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = [],
        $method = 'POST'
    );
}
