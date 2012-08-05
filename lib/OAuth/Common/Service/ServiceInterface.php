<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
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
    public function getAuthorizationUrl( array $additionalParameters = [] );

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
     * Sends an authenticated request to the given endpoint using either the stored token or the given token.
     *
     * @abstract
     * @param UriInterface $uri
     * @param array $bodyParams
     * @param string $method
     * @param array $extraHeaders
     * @param \OAuth\Common\Token\TokenInterface $token
     * @return string
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function sendAuthenticatedRequest(UriInterface $uri, array $bodyParams, $method = 'POST', $extraHeaders = [], TokenInterface $token = null);
}
