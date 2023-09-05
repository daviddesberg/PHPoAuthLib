<?php

namespace OAuth\OAuth1\Service;

use DateTime;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Service\AbstractService as BaseAbstractService;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\OAuth1\Token\TokenInterface;

abstract class AbstractService extends BaseAbstractService implements ServiceInterface
{
    /** @const OAUTH_VERSION */
    const OAUTH_VERSION = 1;

    /** @var SignatureInterface */
    protected $signature;

    /** @var null|UriInterface */
    protected $baseApiUri;

    /** @var string */
    protected $signatureMethod = 'HMAC-SHA1';

    /**
     * {@inheritdoc}
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        ?UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage);

        $this->signature = $signature;
        $this->baseApiUri = $baseApiUri;

        $this->signature->setHashingAlgorithm($this->getSignatureMethod());
    }

    /**
     * {@inheritdoc}
     */
    public function requestRequestToken()
    {
        $authorizationHeader = ['Authorization' => $this->buildAuthorizationHeaderForTokenRequest()];
        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $responseBody = $this->httpClient->retrieveResponse($this->getRequestTokenEndpoint(), [], $headers);

        $token = $this->parseRequestTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = [])
    {
        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($additionalParameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function requestAccessToken($token, $verifier, $tokenSecret = null)
    {
        if (null === $tokenSecret) {
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getRequestTokenSecret();
        }
        $this->signature->setTokenSecret($tokenSecret);

        $bodyParams = [
            'oauth_verifier' => $verifier,
        ];

        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
                'POST',
                $this->getAccessTokenEndpoint(),
                $this->storage->retrieveAccessToken($this->service()),
                $bodyParams
            ),
        ];

        $headers = array_merge($authorizationHeader, $this->getExtraOAuthHeaders());

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $bodyParams, $headers);

        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    /**
     * Refreshes an OAuth1 access token.
     *
     * @return TokenInterface $token
     */
    public function refreshAccessToken(TokenInterface $token)
    {
    }

    /**
     * Sends an authenticated API request to the path provided.
     * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
     *
     * @param string|UriInterface $path
     * @param string              $method       HTTP method
     * @param array               $body         Request body if applicable (key/value pairs)
     * @param array               $extraHeaders Extra headers if applicable.
     *                                          These will override service-specific any defaults.
     *
     * @return string
     */
    public function request($path, $method = 'GET', $body = null, array $extraHeaders = [])
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);

        /** @var StdOAuth1Token $token */
        $token = $this->storage->retrieveAccessToken($this->service());
        $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);
        $authorizationHeader = [
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body),
        ];
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
     * @return string
     */
    protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = [])
    {
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        $parameters = array_merge($parameters, $extraParameters);
        $parameters['oauth_signature'] = $this->signature->getSignature(
            $this->getRequestTokenEndpoint(),
            $parameters,
            'POST'
        );

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach ($parameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';

            $delimiter = ', ';
        }

        return $authorizationHeader;
    }

    /**
     * Builds the authorization header for an authenticated API request.
     *
     * @param string         $method
     * @param UriInterface   $uri        The uri the request is headed
     * @param array          $bodyParams Request body if applicable (key/value pairs)
     *
     * @return string
     */
    protected function buildAuthorizationHeaderForAPIRequest(
        $method,
        UriInterface $uri,
        TokenInterface $token,
        $bodyParams = null
    ) {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $authParameters = $this->getBasicAuthorizationHeaderInfo();
        if (isset($authParameters['oauth_callback'])) {
            unset($authParameters['oauth_callback']);
        }

        $authParameters = array_merge($authParameters, ['oauth_token' => $token->getAccessToken()]);

        $signatureParams = (is_array($bodyParams)) ? array_merge($authParameters, $bodyParams) : $authParameters;
        $authParameters['oauth_signature'] = $this->signature->getSignature($uri, $signatureParams, $method);

        if (is_array($bodyParams) && isset($bodyParams['oauth_session_handle'])) {
            $authParameters['oauth_session_handle'] = $bodyParams['oauth_session_handle'];
            unset($bodyParams['oauth_session_handle']);
        }

        $authorizationHeader = 'OAuth ';
        $delimiter = '';

        foreach ($authParameters as $key => $value) {
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
        $dateTime = new DateTime();
        $headerParameters = [
            'oauth_callback' => $this->credentials->getCallbackUrl(),
            'oauth_consumer_key' => $this->credentials->getConsumerId(),
            'oauth_nonce' => $this->generateNonce(),
            'oauth_signature_method' => $this->getSignatureMethod(),
            'oauth_timestamp' => $dateTime->format('U'),
            'oauth_version' => $this->getVersion(),
        ];

        return $headerParameters;
    }

    /**
     * Pseudo random string generator used to build a unique string to sign each request.
     *
     * @param int $length
     *
     * @return string
     */
    protected function generateNonce($length = 32)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        $nonce = '';
        $maxRand = strlen($characters) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $nonce .= $characters[mt_rand(0, $maxRand)];
        }

        return $nonce;
    }

    /**
     * @return string
     */
    protected function getSignatureMethod()
    {
        return $this->signatureMethod;
    }

    /**
     * Set the signature method.
     * Currently supported: 'HMAC-SHA1' and 'HMAC-SHA256'
     *
     * @param string $method
     */
    protected function setSignatureMethod($method)
    {
        $this->signatureMethod = (string) $method;
    }

    /**
     * Parses the request token response and returns a TokenInterface.
     * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
     * parsing logic is contained in the access token parser.
     *
     * @abstract
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     */
    abstract protected function parseRequestTokenResponse($responseBody);

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     */
    abstract protected function parseAccessTokenResponse($responseBody);
}
