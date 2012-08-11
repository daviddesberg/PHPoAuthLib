<?php
/**
 * OAuth2 service implementation for Twitter.
 *
 * PHP version 5.4
 *
 * @category   OAuth
 * @package    OAuth1
 * @subpackage Service
 * @author     Lusitanian <alusitanian@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\OAuth1\Service;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

/**
 * OAuth2 service implementation for Twitter.
 *
 * @category   OAuth
 * @package    OAuth1
 * @subpackage Service
 * @author     Lusitanian <alusitanian@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Twitter extends AbstractService
{
    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getRequestTokenEndpoint()
    {
        return new Uri('https://api.twitter.com/oauth/request_token');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.twitter.com/oauth/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.twitter.com/oauth/access_token');
    }

    /**
     * We need a separate request token parser only to verify the `oauth_callback_confirmed` parameter. For the actual
     * parsing we can just use the default access token parser.
     *
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth1\Token\StdOAuth1Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] != 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }

        return $this->parseAccessTokenResponse($responseBody);
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth1\Token\StdOAuth1Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth1Token();

        $token->setRequestToken( $data['oauth_token'] );
        $token->setRequestTokenSecret( $data['oauth_token_secret'] );

        $token->setEndOfLife(StdOAuth1Token::EOL_NEVER_EXPIRES);
        unset( $data['oauth_token'], $data['oauth_token_secret'] );
        $token->setExtraParams( $data );

        return $token;
    }

    /**
     * Used to configure response type -- we want JSON from github, default is query string format
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }
}
