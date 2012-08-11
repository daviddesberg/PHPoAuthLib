<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth1\Service;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\Signature;

/**
 * Defines the common methods across OAuth 1 services.
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
     * @param \OAuth\Common\Http\Client\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param \OAuth\OAuth1\Signature\Signature $signature
     * @abstract
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, Signature $signature);

    /**
     * Retrieves and stores/returns the OAuth1 request token.
     *
     * @abstract
     * @return TokenInterface $token
     * @throws InvalidTokenResponseException
     */
    public function requestRequestToken();

    /**
     * Retrieves and stores/returns the OAuth2 access token after a successful authorization.
     *
     * @abstract
     * @param string $token The request token from the callback.
     * @param string $verifier
     * @param string $tokenSecret
     * @return TokenInterface $token
     * @throws InvalidTokenResponseException
     */
    public function requestAccessToken($token, $verifier, $tokenSecret);

    /**
     * @abstract
     * @return UriInterface
     */
    public function getRequestTokenEndpoint();

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
