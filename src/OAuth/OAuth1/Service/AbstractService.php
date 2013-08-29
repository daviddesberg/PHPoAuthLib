<?php
namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\TokenInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Service\AbstractService as BaseAbstractService;

abstract class AbstractService extends BaseAbstractService implements ServiceInterface
{

    /** @const OAUTH_VERSION */
    const OAUTH_VERSION = 1;

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
        parent::__construct($credentials, $httpClient, $storage);

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
        $authorizationHeader = array('Authorization' => $this->buildAuthorizationHeaderForTokenRequest());
        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $responseBody = $this->httpClient->retrieveResponse($this->getRequestTokenEndpoint(), array(), $headers);

        $token = $this->parseRequestTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @param array $additionalParameters
     * @return string
     */
    public function getAuthorizationUri( array $additionalParameters = array() )
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
     * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
     *
     * @abstract
     * @param string $token The request token from the callback.
     * @param string $verifier
     * @param string $tokenSecret
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestAccessToken($token, $verifier, $tokenSecret = null)
    {
        if(is_null($tokenSecret)) {
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getRequestTokenSecret();
        }
        $this->signature->setTokenSecret($tokenSecret);

        $extraAuthenticationHeaders = array(
            'oauth_token' => $token,
        );

        $bodyParams = array(
            'oauth_verifier' => $verifier,
        );

        $authorizationHeader = array(
            'Authorization' =>
                $this->buildAuthorizationHeaderForAPIRequest(
                    'POST',
                    $this->getAccessTokenEndpoint(),
                    $this->storage->retrieveAccessToken($this->service()),
                    $bodyParams
            )
        );

        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $bodyParams, $headers);

        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

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
     */
    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);

        /** @var $token \OAuth\OAuth1\Token\StdOAuth1Token */
        $token = $this->storage->retrieveAccessToken($this->service());
        $extraHeaders = array_merge( $this->getExtraApiHeaders(), $extraHeaders );
        $authorizationHeader = array('Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body));
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
        return array();
    }

    /**
     * Return any additional headers always needed for this service implementation's API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders()
    {
        return array();
    }

    /**
     * Builds the authorization header for getting an access or request token.
     *
     * @param array $extraParameters
     * @return string
     */
    protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = array())
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
     * @param $bodyParams Request body if applicable (key/value pairs)
     * @return string
     */
    protected function buildAuthorizationHeaderForAPIRequest($method, UriInterface $uri, TokenInterface $token, $bodyParams = null)
    {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        if( isset($parameters['oauth_callback'] ) ){
            unset($parameters['oauth_callback']);
        }

        $parameters = array_merge($parameters, array('oauth_token' => $token->getAccessToken()) );

        $mergedParams = (is_array($bodyParams)) ? array_merge($parameters, $bodyParams) : $parameters;

        $parameters['oauth_signature'] = $this->signature->getSignature($uri, $mergedParams, $method);

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
        $dateTime = new \DateTime();
        $headerParameters = array(
            'oauth_callback'         => $this->credentials->getCallbackUrl(),
            'oauth_consumer_key'     => $this->credentials->getConsumerId(),
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_signature_method' => $this->getSignatureMethod(),
            'oauth_timestamp'        => $dateTime->format('U'),
            'oauth_version'          => $this->getVersion(),
        );

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
