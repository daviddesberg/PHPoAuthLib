<?php
/**
 * @author Pieter Hordijl <info@pieterhordijk.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Service;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri;

/**
 * OAuth2 service implementation for Bitly
 */
class Bitly extends AbstractService
{
    /**
     * @return \OAuth\Common\Http\Uri|\OAuth\Common\Http\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://bitly.com/oauth/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri|\OAuth\Common\Http\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api-ssl.bitly.com/oauth/access_token');
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode( $responseBody, true );

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();


        $token->setAccessToken( $data['access_token'] );
        // I'm invincible!!!
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset( $data['access_token'] );
        $token->setExtraParams( $data );

        return $token;
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
        $bodyParams = [
            'code' => $code,
            'client_id' => $this->credentials->getConsumerId(),
            'client_secret' => $this->credentials->getConsumerSecret(),
            'redirect_uri' => $this->credentials->getCallbackUrl(),
            'grant_type' => 'authorization_code',
        ];

        $responseBody = $this->httpClient->retrieveResponse($this->getAccessTokenEndpoint(), $bodyParams, $this->getExtraOAuthHeaders());

        // we can scream what we want that we want bitly to return a json encoded string (format=json), but the
        // bastard doesn't seem to like screaming, hence we need to manually parse the result
        $parsedResult = [];
        parse_str($responseBody, $parsedResult);

        $token = $this->parseAccessTokenResponse( json_encode($parsedResult) );
        $this->storage->storeAccessToken( $token );

        return $token;
    }

    /**
     * Returns whether or not the passed scope value is valid.
     *
     * @param $scope
     * @return bool
     */
    public function isValidScope($scope)
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        return in_array( $scope, $reflectionClass->getConstants() );
    }

    /**
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }
}
