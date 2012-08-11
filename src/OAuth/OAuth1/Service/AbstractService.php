<?php
/**
 * @author David Desberg <david@thedesbergs.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Service\Exception\InvalidScopeException;
use OAuth\Common\Service\Exception\MissingRefreshTokenException;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;
use OAuth\OAuth1\Signature\Signature;

/**
 * AbstractService class for OAuth 1, implements basic methods in compliance with that protocol
 */
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
     * @var \OAuth\Common\Http\Client\ClientInterface
     */
    protected $httpClient;

    /**
     * @var \OAuth\OAuth1\Signature\Signature
     */
    protected $signature;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\Client\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param \OAuth\OAuth1\Signature\Signature $signature
     * @throws InvalidScopeException
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, Signature $signature)
    {
        $this->credentials  = $credentials;
        $this->httpClient   = $httpClient;
        $this->storage      = $storage;
        $this->signature    = $signature;

        $this->signature->setHashingAlgorithm($this->getSignatureMethod());
    }

    /**
     * Retrieves and stores the OAuth1 request token obtained from the service.
     *
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestRequestToken()
    {
        $headerParameters = $this->getAuthorizationHeaderInfo();
        $headerParameters['oauth_signature'] = $this->signature->getSignature($this->getRequestTokenEndpoint(), null, $headerParameters);

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach($headerParameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';

            $delimiter = ', ';
        }

        $responseBody = $this->httpClient->retrieveResponse($this->getRequestTokenEndpoint(), [], ['Authorization' => $authorizationHeader]);

        $token = $this->parseRequestTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @param array $additionalParameters
     * @return string
     */
    public function getAuthorizationUri( array $additionalParameters = [] )
    {
        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach($additionalParameters as $key => $val)
        {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

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
    public function requestAccessToken($token, $verifier, $tokenSecret)
    {
        $this->signature->setTokenSecret($tokenSecret);

        $headerParameters = $this->getAuthorizationHeaderInfo($token);
        $headerParameters['oauth_signature'] = $this->signature->getSignature($this->getRequestTokenEndpoint(), null, $headerParameters);

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach($headerParameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';

            $delimiter = ', ';
        }

        $bodyParams = [
            'oauth_verifier' => $verifier,
        ];

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $bodyParams, ['Authorization' => $authorizationHeader]);

        $token = $this->parseAccessTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Sends an authenticated request to the given endpoint using stored token.
     *
     * @param UriInterface $uri
     * @param array $bodyParams
     * @param string $method
     * @param array $extraHeaders
     * @return string
     * @throws \OAuth\Common\Token\Exception\ExpiredTokenException
     */
    public function sendAuthenticatedRequest(UriInterface $uri, array $bodyParams, $method = 'POST', $extraHeaders = [])
    {
        $token = $this->storage->retrieveAccessToken();

        if( ( $token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES ) &&
            ( $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN ) &&
            ( time() > $token->getEndOfLife() ) ) {

            throw new ExpiredTokenException('Token expired on ' . date('m/d/Y', $token->getEndOfLife()) . ' at ' . date('h:i:s A', $token->getEndOfLife()) );
        }

        // add the token where it may be needed
        if( static::AUTHORIZATION_METHOD_HEADER_OAUTH === $this->getAuthorizationMethod() ) {
            $extraHeaders = array_merge( ['Authorization' => 'OAuth ' . $token->getAccessToken() ], $extraHeaders );
        } elseif( static::AUTHORIZATION_METHOD_QUERY_STRING === $this->getAuthorizationMethod() ) {
            $uri->addToQuery( 'access_token', $token->getAccessToken() );
        } elseif( static::AUTHORIZATION_METHOD_HEADER_BEARER === $this->getAuthorizationMethod() ) {
            $extraHeaders = array_merge( ['Authorization' => 'Bearer ' . $token->getAccessToken() ], $extraHeaders );
        }

        $extraHeaders = array_merge( $extraHeaders, $this->getExtraApiHeaders() );

        return $this->httpClient->retrieveResponse($uri, $bodyParams, $extraHeaders, $method);
    }

    /**
     * Refreshes an OAuth1 access token.
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

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $parameters, $this->getExtraOAuthHeaders());
        $token = $this->parseAccessTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Return any additional headers always needed for this service implementation's OAuth calls.
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return [];
    }

    /**
     * Return any additional headers always needed for this service implementation's API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders()
    {
        return [];
    }

    /**
     * Parses the request token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\Common\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseRequestTokenResponse($responseBody);

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\Common\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseAccessTokenResponse($responseBody);

    /**
     * Returns a class constant from ServiceInterface defining the authorization method used for the API
     * Header is the sane default.
     *
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_OAUTH;
    }

    /**
     * Builds the authorization header array.
     *
     * @param string $token
     * @return array
     */
    protected function getAuthorizationHeaderInfo($token = null)
    {
        $headerParameters = [
            'oauth_callback'            => $this->credentials->getCallbackUrl(),
            'oauth_consumer_key'        => $this->credentials->getConsumerId(),
            'oauth_nonce'               => $this->generateNonce(),
            'oauth_signature_method'    => $this->getSignatureMethod(),
            'oauth_timestamp'           => (new \DateTime())->format('U'),
            'oauth_version'             => '1.0',
        ];

        if ($token !== null) {
            $headerParameters['oauth_token'] = $token;
        }

        return $headerParameters;
    }

    /**
     * Pseudo random string generator used to build a unique string to sign each request
     *
     * @param int $length
     * @return string
     */
    protected function generateNonce($length = 32)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $nonce = '';
        $maxRand = strlen($characters)-1;
        for($i = 0; $i < $length; $i++) {
            $nonce.= $characters[rand(0, $maxRand)];
        }

        return $nonce;
    }

    /**
     * @return string
     */
    protected function getSignatureMethod()
    {
        return 'HMAC-SHA1';
    }
}
