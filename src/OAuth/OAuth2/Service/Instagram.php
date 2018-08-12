<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

class Instagram extends AbstractService
{
    /**
     * Defined scopes
     * @link http://instagram.com/developer/authentication/#scope
     */
    const SCOPE_BASIC         = 'basic';
    const SCOPE_PUBLIC_CONTENT = 'public_content';
    const SCOPE_COMMENTS      = 'comments';
    const SCOPE_RELATIONSHIPS = 'relationships';
    const SCOPE_LIKES         = 'likes';
    const SCOPE_FOLLOWER_LIST = 'follower_list';
    
    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->stateParameterInAuthUrl = true;

        if( $this->baseApiUri === null ) {
            $this->baseApiUri = new Uri('https://api.instagram.com/v1/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.instagram.com/oauth/authorize/');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.instagram.com/oauth/access_token');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        // Instagram tokens evidently never expire...
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data['access_token']);

        $token->setExtraParams($data);

        return $token;
    }
}
