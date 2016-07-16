<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class Echosign extends AbstractService
{
    /**
     * Defined scopes, see https://secure.echosign.com/public/static/oauthDoc.jsp#scopes for definitions.
     */

    const SCOPE_AGREEMENT_READ = 'agreement_read:self';
    const SCOPE_AGREEMENT_RETENTION = 'agreement_retention:self';
    const SCOPE_AGREEMENT_SEND = 'agreement_send:self';
    const SCOPE_AGREEMENT_VAULT = 'agreement_vault:self';
    const SCOPE_AGREEMENT_WRITE = 'agreement_write:self';
    const SCOPE_LIBRARY_READ = 'library_read:self';
    const SCOPE_LIBRARY_WRITE = 'library_write:self';
    const SCOPE_USER_LOGIN = 'user_login:self';
    const SCOPE_USER_READ = 'user_read:self';
    const SCOPE_USER_WRITE = 'user_write:self';
    const SCOPE_WIDGET_READ = 'widget_read:self';
    const SCOPE_WIDGET_WRITE = 'widget_write:self';
    
    const SCOPE_AGREEMENT_READ_GROUP = 'agreement_read:group';
    const SCOPE_AGREEMENT_RETENTION_GROUP = 'agreement_retention:group';
    const SCOPE_AGREEMENT_SEND_GROUP = 'agreement_send:group';
    const SCOPE_AGREEMENT_VAULT_GROUP = 'agreement_vault:group';
    const SCOPE_AGREEMENT_WRITE_GROUP = 'agreement_write:group';
    const SCOPE_LIBRARY_READ_GROUP = 'library_read:group';
    const SCOPE_LIBRARY_WRITE_GROUP = 'library_write:group';
    const SCOPE_USER_LOGIN_GROUP = 'user_login:group';
    const SCOPE_USER_READ_GROUP = 'user_read:group';
    const SCOPE_USER_WRITE_GROUP = 'user_write:group';
    const SCOPE_WIDGET_READ_GROUP = 'widget_read:group';
    const SCOPE_WIDGET_WRITE_GROUP = 'widget_write:group';
    
    const SCOPE_AGREEMENT_READ_ACCOUNT = 'agreement_read:account';
    const SCOPE_AGREEMENT_RETENTION_ACCOUNT = 'agreement_retention:account';
    const SCOPE_AGREEMENT_SEND_ACCOUNT = 'agreement_send:account';
    const SCOPE_AGREEMENT_VAULT_ACCOUNT = 'agreement_vault:account';
    const SCOPE_AGREEMENT_WRITE_ACCOUNT = 'agreement_write:account';
    const SCOPE_LIBRARY_READ_ACCOUNT = 'library_read:account';
    const SCOPE_LIBRARY_WRITE_ACCOUNT = 'library_write:account';
    const SCOPE_USER_LOGIN_ACCOUNT = 'user_login:account';
    const SCOPE_USER_READ_ACCOUNT = 'user_read:account';
    const SCOPE_USER_WRITE_ACCOUNT = 'user_write:account';
    const SCOPE_WIDGET_READ_ACCOUNT = 'widget_read:account';
    const SCOPE_WIDGET_WRITE_ACCOUNT = 'widget_write:account';

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.echosign.com/api/rest/v3 ');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://secure.echosign.com/oauth');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://secure.echosign.com/oauth/token');
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_OAUTH;
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
        // tokens evidently never expire... TODO: not sure about this
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data['access_token']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return array('Accept' => 'application/json');
    }


}
