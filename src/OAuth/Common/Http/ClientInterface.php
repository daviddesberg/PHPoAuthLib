<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Http;
use OAuth\Common\Http\Exception\TokenResponseException;

/**
 * Any HTTP clients to be used with the library should implement this interface.
 */
interface ClientInterface
{
    /**
     * Any implementing HTTP providers should send a POST request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @abstract
     * @param UriInterface $endpoint
     * @param mixed $requestBody
     * @param array $extraHeaders
     * @param string $method
     * @return string
     * @throws TokenResponseException
     */
    public function retrieveResponse(UriInterface $endpoint, $requestBody, array $extraHeaders = [], $method = 'POST');
}
