<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;

class Yahoo extends AbstractService
{
    /**
     * Defined scopes
     *
     * @link https://developer.yahoo.com/oauth2/guide/yahoo_scopes/
     */

    // OpenId
    const SCOPE_OPENID = 'openid';
    // Contacts
    const SCOPE_CONTACTS_READ = 'sdct-r';
    const SCOPE_CONTACTS_READ_WRITE = 'sdct-w';
    // Fantasy Sports
    const SCOPE_FANTASY_READ = 'fspt-r';
    const SCOPE_FANTASY_READ_WRITE = 'fspt-w';
    // Finance
    const SCOPE_FINANCE_READ = 'yfin-r';
    const SCOPE_FINANCE_READ_WRITE = 'yfin-w';
    // Yahoo Gemini Advertising
    const SCOPE_GEMINIADS_READ_WRITE = 'admg-w';
    // Gemini Publishers
    const SCOPE_GEMINIPUBLISHERS_READ = 'gpub-r';
    // Mail
    const SCOPE_MAIL_READ = 'openid mail-r';
    const SCOPE_MAIL_READ_WRITE = 'mail-w';
    const SCOPE_MAIL_FULL = 'mail-x';
    // Messenger
    const SCOPE_MESSENGER_READ_WRITE = 'msgr-w';
    // Profiles (Social Directory)
    const SCOPE_PROFILES_PUBLIC_READ = 'sdps-r';
    const SCOPE_PROFILES_PUBLIC_READ_WRITE = 'sdps-w';
    const SCOPE_PROFILES_PRIVATE_READ_WRITE = 'sdpp-w';
    // Relationships (Social Directory)
    const SCOPE_RELATIONSHIPS_READ_WRITE = 'sdrl-w';

    /**
    * {@inheritdoc}
    */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.login.yahoo.com/oauth2/request_auth');
    }

    /**
    * {@inheritdoc}
    */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.login.yahoo.com/oauth2/get_token');
    }

    /**
    * {@inheritdoc}
    */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
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
        $token->setLifetime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
    * {@inheritdoc}
    */
    protected function getExtraOAuthHeaders()
    {
        $encodedCredentials = base64_encode(
            $this->credentials->getConsumerId() . ':' . $this->credentials->getConsumerSecret()
        );
        return array('Authorization' => 'Basic ' . $encodedCredentials);
    }
}
