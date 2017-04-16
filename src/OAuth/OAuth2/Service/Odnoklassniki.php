<?php
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;
use OAuth\Common\Token\Exception\MissingApplicationKeyException;

class Odnoklassniki extends AbstractService
{

    protected $applicationKey;

    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if( null === $baseApiUri ) {
            $this->baseApiUri = new Uri('http://api.odnoklassniki.ru/api/');
        }
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('http://www.odnoklassniki.ru/oauth/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('http://api.odnoklassniki.ru/oauth/token.do');
    }

    /**
     * @param string $appKey
     * @return $this
     */
    public function setApplicationKey($appKey)
    {
        $this->applicationKey = $appKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();

        $token->setAccessToken( $data['access_token'] );
        $token->setLifeTime(1800); // token has fixed expire and it's value is not returned by service

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );

        if ( $this->applicationKey ) {
            $data['application_key'] = $this->applicationKey;
        }
        //unset( $data['expires_in'] );
        $token->setExtraParams( $data );

        return $token;
    }

    /**
     * This is a full override of parent::request because Odnoklassniki API requires different logic of api requests
     *
     * Sends an authenticated API request to the path provided.
     * If the path provided is not an absolute URI, the base API Uri (must be passed into constructor) will be used.
     *
     * @param $path string|UriInterface
     * @param string $method HTTP method
     * @param array $body Request body if applicable (key/value pairs)
     * @param array $extraHeaders Extra headers if applicable. These will override service-specific any defaults.
     * @return string
     * @throws ExpiredTokenException
     * @throws MissingApplicationKeyException
     * @throws Exception
     */
    public function request($path, $method = 'GET', array $body = array(), array $extraHeaders = array())
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
        $token = $this->storage->retrieveAccessToken($this->service());
        $extraParams = $token->getExtraParams();
        $storedAppKey = isset($extraParams['application_key']) ? $extraParams['application_key'] : null;

        if ( empty($body['application_key']) ) {
            if ( $this->applicationKey ) {
                $body['application_key'] = $this->applicationKey;
            }
            elseif ( $storedAppKey ) {
                $body['application_key'] = $storedAppKey;
            }
            else {
                throw new MissingApplicationKeyException('Application key not found');
            }
        }

        if ( strpos($path, '?') ) {
            $query = explode('?', $path);
            $query = end($query);
            parse_str($query, $queryParams);
            $body += $queryParams;
        }

        if( ( $token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES ) &&
            ( $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN ) &&
            ( time() > $token->getEndOfLife() ) ) {

            throw new ExpiredTokenException('Token expired on ' . date('m/d/Y', $token->getEndOfLife()) . ' at ' . date('h:i:s A', $token->getEndOfLife()) );
        }

        ksort($body);

        $sig = '';
        foreach ($body as $k=>$v) {
            $sig .= $k . '=' . $v;
        }
        $sig = md5($sig . md5( $token->getAccessToken() . $this->credentials->getConsumerSecret() ) );
        $body['sig'] = $sig;

        $uri->addToQuery( 'access_token', $token->getAccessToken() );
        foreach ($body as $qK => $qV) {
            $uri->addToQuery( $qK, $qV );
        }

        $body = array();

        $extraHeaders = array_merge( $this->getExtraApiHeaders(), $extraHeaders );

        return $this->httpClient->retrieveResponse($uri, $body, $extraHeaders, $method);
    }


}