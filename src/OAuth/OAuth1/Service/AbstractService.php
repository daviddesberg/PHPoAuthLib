<?php
/**
 * @author David Desberg <david@daviddesberg.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\TokenInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Exception\Exception;

/**
 * AbstractService class for OAuth 1, implements basic methods in compliance with that protocol
 */
abstract class AbstractService implements ServiceInterface
{
    /** @var \OAuth\Common\Consumer\Credentials */
    protected $credentials;

    /** @var \OAuth\Common\Storage\TokenStorageInterface */
    protected $storage;

    /**
     * @var \OAuth\Common\Http\Client\ClientInterface
     */
    protected $httpClient;

    /** @var \OAuth\OAuth1\Signature\SignatureInterface */
    protected $signature;

    /** @var \OAuth\Common\Http\Uri\UriInterface|null */
    protected $baseApiUri;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\Client\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param \OAuth\OAuth1\Signature\SignatureInterface $signature
     * @param UriInterface|null $baseApiUri
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, SignatureInterface $signature, UriInterface $baseApiUri = null)
    {
        $this->credentials  = $credentials;
        $this->httpClient   = $httpClient;
        $this->storage      = $storage;
        $this->signature    = $signature;
        $this->baseApiUri   = $baseApiUri;

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
        $authorizationHeader = ['Authorization' => $this->buildAuthorizationHeaderForTokenRequest() ];
        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $responseBody = $this->httpClient->retrieveResponse($this->getRequestTokenEndpoint(), [], $headers);

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
     * @throws TokenResponseException
     */
    public function requestAccessToken($token, $verifier, $tokenSecret)
    {
        $this->signature->setTokenSecret($tokenSecret);

        $extraAuthenticationHeaders = [
            'oauth_token' => $token,
        ];

        $authorizationHeader = ['Authorization' => $this->buildAuthorizationHeaderForTokenRequest($extraAuthenticationHeaders)];
        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $bodyParams = [
            'oauth_verifier' => $verifier,
        ];

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $bodyParams, $headers);

        $token = $this->parseAccessTokenResponse( $responseBody );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Sends an authenticated API request to the path provided.
     * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
     * @param $path string|UriInterface
     * @param string $method HTTP method
     * @param array $body Request body if applicable (key/value pairs)
     * @param array $extraHeaders Extra headers if applicable. These will override service-specific any defaults.
     * @return string
     * @throws Exception
     */
    public function request($path, $method = 'GET', array $body = [], array $extraHeaders = [])
    {
        if( $path instanceof UriInterface ) {
            $uri = $path;
        } elseif( 0 === strpos('http://', $path) || 0 === strpos('https://', $path)  ) {
            // @todo uncouple this.
            $uri = new Uri($path);
        } else {
            if( null === $this->baseApiUri ) {
                throw new Exception('An absolute URI must be passed to ServiceInterface::request as no baseApiUri is set.');
            }

            $uri = clone $this->baseApiUri;
            if( false !== strpos($path, '?') ) {
                $parts = explode('?', $uri, 2);
                $path = $parts[0];
                $query = $parts[1];
                $uri->setQuery($query);
            }

            if( $path[0] === '/' ) {
                $path = substr($path, 1);
            }

            $uri->setPath($uri->getPath() . $path);
        }

        /** @var $token \OAuth\OAuth1\Token\StdOAuth1Token */
        $token = $this->storage->retrieveAccessToken();
        $extraHeaders = array_merge( $this->getExtraApiHeaders(), $extraHeaders );
        $authorizationHeader = ['Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body) ];
        $headers = array_merge($authorizationHeader, $extraHeaders);

        return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);
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
     * Builds the authorization header for getting an access or request token.
     *
     * @param array $extraParameters
     * @return string
     */
    protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = [])
    {
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        $parameters = array_merge($parameters, $extraParameters);
        $parameters['oauth_signature'] = $this->signature->getSignature($this->getRequestTokenEndpoint(), $parameters, 'POST');

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach($parameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';

            $delimiter = ', ';
        }

        return $authorizationHeader;
    }

    /**
     * Builds the authorization header for an authenticated API request
     * @param string $method
     * @param UriInterface $uri the uri the request is headed
     * @param \OAuth\OAuth1\Token\TokenInterface $token
     * @param $bodyParams array
     * @return string
     */
    protected function buildAuthorizationHeaderForAPIRequest($method, UriInterface $uri, TokenInterface $token, $bodyParams)
    {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        if( isset($parameters['oauth_callback'] ) ){
            unset($parameters['oauth_callback']);
        }

        $parameters = array_merge($parameters, [ 'oauth_token' => $token->getAccessToken() ] );
        $parameters['oauth_signature'] = $this->signature->getSignature($uri, array_merge($parameters, $bodyParams), $method);

        $authorizationHeader = 'OAuth ';
        $delimiter = '';

        foreach($parameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';
            $delimiter = ', ';
        }

        return $authorizationHeader;
    }

    /**
     * Builds the authorization header array.
     *
     * @return array
     */
    protected function getBasicAuthorizationHeaderInfo()
    {
        $headerParameters = [
            'oauth_callback'            => $this->credentials->getCallbackUrl(),
            'oauth_consumer_key'        => $this->credentials->getConsumerId(),
            'oauth_nonce'               => $this->generateNonce(),
            'oauth_signature_method'    => $this->getSignatureMethod(),
            'oauth_timestamp'           => (new \DateTime())->format('U'),
            'oauth_version'             => $this->getVersion(),
        ];

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

    /**
     * This returns the version used in the authorization header of the requests
     *
     * @return string
     */
    protected function getVersion()
    {
        return '1.0';
    }


    /**
     * Parses the request token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\OAuth1\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseRequestTokenResponse($responseBody);

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\OAuth1\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseAccessTokenResponse($responseBody);
}
