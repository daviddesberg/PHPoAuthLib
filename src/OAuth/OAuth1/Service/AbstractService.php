<?php

namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\CredentialsInterface;
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

    /** @var SignatureInterface */
    protected $signature;

    /** @var UriInterface|null */
    protected $baseApiUri;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage);

        $this->signature = $signature;
        $this->baseApiUri = $baseApiUri;

        $this->signature->setHashingAlgorithm($this->getSignatureMethod());
    }

    /**
     * {@inheritDoc}
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
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = array())
    {
        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($additionalParameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    /**
     * {@inheritDoc}
     */
    public function requestAccessToken($token, $verifier, $tokenSecret = null)
    {
        if (is_null($tokenSecret)) {
            $storedRequestToken = $this->storage->retrieveAccessToken($this->service());
            $tokenSecret = $storedRequestToken->getRequestTokenSecret();
        }
        $this->signature->setTokenSecret($tokenSecret);

        $bodyParams = array(
            'oauth_verifier' => $verifier,
        );

        $authorizationHeader = array(
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest(
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
     *
     * @param string|UriInterface $path
     * @param string              $method       HTTP method
     * @param array               $body         Request body if applicable (key/value pairs)
     * @param array               $extraHeaders Extra headers if applicable.
     *                                          These will override service-specific any defaults.
     *
     * @return string
     */
    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array(), $querystring = array() )
    {    
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
        
        /** @var $token StdOAuth1Token */
        $token = $this->storage->retrieveAccessToken($this->service());
        $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);
        $authorizationHeader = array(
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body)
        );
        
        $headers = array_merge($authorizationHeader, $extraHeaders);
        
        
        // if querystring array is not empty then we use an API wich require oauth parameters directly in URI
        if ( !empty ( $querystring ) ){
            $uri = $this->determineRequestUriFromPath($path . "?" . $querystring['action'], $this->baseApiUri);
        
            // Init value to build signature according to withings spec
            $dateTime = new \DateTime();
            $sigUri  = $this->baseApiUri . $path;
            $time = time();
            $nonce = $this->generateNonce();

            // Params for signature uri generation
            $params = "&oauth_consumer_key=" . $this->credentials->getConsumerId();
            $params .= "&oauth_nonce=" . $nonce;
            $params .= "&oauth_signature_method=HMAC-SHA1";
            $params .= "&oauth_timestamp=" . $time;
            $params .= "&oauth_token=" . $token->getAccessToken();
            $params .= "&oauth_version=1.0";
            
            //withings specifics
            if ( $querystring['api'] == "withings" )
                $params .= "&userid=" . $querystring['userid'];

            // urlencode of the full query for signature
            $sigUri = "GET&" . rawurlencode( $sigUri ) . "&" . rawurlencode( $querystring['action'] . $params );

            // secrets keys for encoding ( app secret + user token secret )
            $keys = array ( $this->credentials->getConsumerSecret() ,  $token->getAccessTokenSecret() );
            $key    = implode ( '&', $keys );
            $signature = rawurlencode (  base64_encode ( $this->hmacsha1( $key, $sigUri ) ) );

            $uri->query .= "&oauth_consumer_key=" . $this->credentials->getConsumerId();
            $uri->query .= "&oauth_nonce=" . $nonce;
            $uri->query .= "&oauth_signature=" . $signature;
            $uri->query .= "&oauth_signature_method=HMAC-SHA1";
            $uri->query .= "&oauth_timestamp=" . $time;
            $uri->query .= "&oauth_token=" . $token->getAccessToken();
            $uri->query .= "&oauth_version=1.0";
            
            //withings specifics
            if ( $querystring['api'] == "withings" )
                $uri->query .= "&userid=" . $querystring['userid'];
        }
        
        return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);
    }
    
    public function hmacsha1($key,$data) {
        $blocksize=64;
        $hashfunc='sha1';
        if (strlen($key)>$blocksize)
            $key=pack('H*', $hashfunc($key));
        $key=str_pad($key,$blocksize,chr(0x00));
        $ipad=str_repeat(chr(0x36),$blocksize);
        $opad=str_repeat(chr(0x5c),$blocksize);
        $hmac = pack(
                    'H*',$hashfunc(
                        ($key^$opad).pack(
                            'H*',$hashfunc(
                                ($key^$ipad).$data
                            )
                        )
                    )
                );
        return $hmac;
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
     *
     * @return string
     */
    protected function buildAuthorizationHeaderForTokenRequest(array $extraParameters = array())
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
     * Builds the authorization header for an authenticated API request
     *
     * @param string         $method
     * @param UriInterface   $uri        The uri the request is headed
     * @param TokenInterface $token
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
        $parameters = $this->getBasicAuthorizationHeaderInfo();
        if (isset($parameters['oauth_callback'])) {
            unset($parameters['oauth_callback']);
        }

        $parameters = array_merge($parameters, array('oauth_token' => $token->getAccessToken()));
        $parameters = (is_array($bodyParams)) ? array_merge($parameters, $bodyParams) : $parameters;
        $parameters['oauth_signature'] = $this->signature->getSignature($uri, $parameters, $method);

        $authorizationHeader = 'OAuth ';
        $delimiter = '';

        foreach ($parameters as $key => $value) {
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
     *
     * @return string
     */
    protected function generateNonce($length = 32)
    {
        $mt = microtime();
        $rand = mt_rand();

        return md5($mt . $rand); // md5s look nicer than numbers
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
     * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
     * parsing logic is contained in the access token parser.
     *
     * @abstract
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     *
     * @throws TokenResponseException
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
     *
     * @throws TokenResponseException
     */
    abstract protected function parseAccessTokenResponse($responseBody);
}
