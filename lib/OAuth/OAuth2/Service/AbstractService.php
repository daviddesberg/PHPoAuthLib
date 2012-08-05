<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\ClientInterface;
use OAuth\Common\Http\UriInterface;
use OAuth\Common\Service\Exception\InvalidScopeException;
use OAuth\Common\Service\Exception\MissingRefreshTokenException;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;
use OAuth\Common\Service\ServiceInterface;

abstract class AbstractService implements ServiceInterface
{
    /**
     * @var \OAuth\Common\Consumer\Credentials
     */
    protected $credentials;

    /**
     * @var \OAuth\Common\Storage\TokenStorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * @var \OAuth\Common\Http\ClientInterface
     */
    protected $httpClient;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param array $scopes
     * @throws InvalidScopeException
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = [])
    {
        $this->credentials = $credentials;
        $this->httpClient = $httpClient;
        $this->storage = $storage;

        foreach($scopes as $scope)
        {
            if( !$this->isValidScope($scope) ) {
                throw new InvalidScopeException('Scope ' . $scope . ' is not valid for service ' . get_class($this) );
            }
        }

        $this->scopes = $scopes;

    }

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @param array $additionalParameters
     * @return string
     */
    public function getAuthorizationUrl( array $additionalParameters = [] )
    {
        $parameters = array_merge($additionalParameters,
            [
                'type' => 'web_server',
                'client_id' => $this->credentials->getConsumerId(),
                'redirect_uri' => $this->credentials->getCallbackUrl(),
                'response_type' => 'code',
            ]
        );

        $parameters['scope'] = implode(' ', $this->scopes);

        // Build the url
        $url = $this->getAuthorizationEndpoint();
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($parameters);

        return $url;
    }


    /**
     * Retrieves and stores the OAuth2 access token after a successful authorization.
     *
     * @param string $code The access code from the callback.
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestAccessToken($code)
    {
        $parameters =
        [
            'code' => $code,
            'client_id' => $this->credentials->getConsumerId(),
            'client_secret' => $this->credentials->getConsumerSecret(),
            'redirect_uri' => $this->credentials->getCallbackUrl(),
            'grant_type' => 'authorization_code',

        ];

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $parameters);
        $token = $this->parseAccessTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Sends an authenticated request to the given endpoint using either the stored token or the given token.
     *
     * @param UriInterface $uri
     * @param array $bodyParams
     * @param string $method
     * @param array $extraHeaders
     * @param \OAuth\Common\Token\TokenInterface $token
     * @return string
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function sendAuthenticatedRequest(UriInterface $uri, array $bodyParams, $method = 'POST', $extraHeaders = [], TokenInterface $token = null)
    {
        if( null === $token ) {
            $token = $this->storage->retrieveAccessToken();
        }

        if( time() > $token->getEndOfLife() ) {
            throw new ExpiredTokenException('Token expired on ' . date('m/d/Y', $token->getEndOfLife()) . ' at ' . date('h:i:s A', $token->getEndOfLife()) );
        }

        // add the token to the request headers
        $extraHeaders = array_merge( ['Authorization' => 'OAuth ' . $token->getAccessToken() ], $extraHeaders );

        return $this->httpClient->retrieveResponse($uri, $bodyParams, $extraHeaders, $method);
    }

    /**
     * Refreshes an OAuth2 access token.
     *
     * @param \OAuth\Common\Token\TokenInterface $token
     * @return \OAuth\Common\Token\TokenInterface $token
     * @throws \OAuth\Common\Service\Exception\MissingRefreshTokenException
     */
    public function refreshAccessToken(TokenInterface $token)
    {
        $refreshToken = $token->getRefreshToken();

        if ( empty( $refreshToken ) ) {
            throw new MissingRefreshTokenException();
        }

        $parameters =
        [
            'grant_type' => 'refresh_token',
            'type' => 'web_server',
            'client_id' => $this->credentials->getConsumerId(),
            'client_secret' => $this->credentials->getConsumerSecret(),
            'refresh_token' => $refreshToken,
        ];

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $parameters);
        $token = $this->parseAccessTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Return whether or not the passed scope value is valid.
     *
     * @param $scope
     * @return bool
     */
    public function isValidScope($scope)
    {
        return true;
    }

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\Common\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseAccessTokenResponse($responseBody);
}
