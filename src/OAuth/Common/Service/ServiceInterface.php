<?php
/**
 * @author David Desberg <david@thedesbergs.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Service;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Http\ClientInterface;
use OAuth\Common\Http\UriInterface;

/**
 * Defines the common methods across any OAuth service, be it version 1 or 2.
 */
interface ServiceInterface
{
    /**
     * Authorization methods for various services
     */
    const AUTHORIZATION_METHOD_HEADER_OAUTH = 0;
    const AUTHORIZATION_METHOD_HEADER_BEARER = 1;
    const AUTHORIZATION_METHOD_QUERY_STRING = 2;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param array $scopes
     * @abstract
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = []);

    /**
     * Retrieves and stores/returns the OAuth2 access token after a successful authorization.
     *
     * @abstract
     * @param string $code The access code from the callback.
     * @return TokenInterface $token
     * @throws InvalidTokenResponseException
     */
    public function requestAccessToken($code);

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @abstract
     * @param array $additionalParameters
     * @return UriInterface
     */
    public function getAuthorizationUri( array $additionalParameters = [] );

    /**
     * @abstract
     * @return UriInterface
     */
    public function getAuthorizationEndpoint();

    /**
     * @abstract
     * @return UriInterface
     */
    public function getAccessTokenEndpoint();

    /**
     * Sends an authenticated request to the given endpoint using stored token.
     *
     * @abstract
     * @param UriInterface $uri
     * @param array $bodyParams
     * @param string $method
     * @param array $extraHeaders
     * @return string
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function sendAuthenticatedRequest(UriInterface $uri, array $bodyParams, $method = 'POST', $extraHeaders = []);
}
