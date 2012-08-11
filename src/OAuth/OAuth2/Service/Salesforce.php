<?php
/**
 * OAuth2 service implementation for Salesforce.
 *
 * PHP version 5.4
 *
 * @category   OAuth
 * @package    OAuth2
 * @subpackage Service
 * @author     David Desberg <david@thedesbergs.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

/**
 * OAuth2 service implementation for Salesforce.
 *
 * @category   OAuth
 * @package    OAuth2
 * @subpackage Service
 * @author     David Desberg <david@thedesbergs.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Salesforce extends AbstractService
{
    /**
     * Defined scopes.
     */
	const SCOPE_API = 'api';
	const SCOPE_CHATTER_API = 'chatter_api';
	const SCOPE_FULL = 'full';
	const SCOPE_ID = 'id';
	const SCOPE_REFRESH_TOKEN = 'refresh_token';
	const SCOPE_VISUAL_FORCE = 'visualforce';
	const SCOPE_WEB = 'web';


    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://login.salesforce.com/services/oauth2/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://login.salesforce.com/services/oauth2/token');
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
        $token->setLifetime( $data['expires_in'] );

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );
        unset( $data['expires_in'] );

        $token->setExtraParams( $data );

        return $token;
    }

    /**
     * Any extra headers for OAuth calls.
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return ['Accept' => 'application/json'];
    }

    /**
     * Any extra headers for API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders()
    {
        return [];
    }

    /**
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }
}
