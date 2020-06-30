<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;

/**
 * Heroku service.
 *
 * @author Thomas Welton <thomaswelton@me.com>
 *
 * @see https://devcenter.heroku.com/articles/oauth
 */
class Heroku extends AbstractService
{
    /**
     * Defined scopes.
     *
     * @see https://devcenter.heroku.com/articles/oauth#scopes
     */
    const SCOPE_GLOBAL = 'global';
    const SCOPE_IDENTITY = 'identity';
    const SCOPE_READ = 'read';
    const SCOPE_WRITE = 'write';
    const SCOPE_READ_PROTECTED = 'read-protected';
    const SCOPE_WRITE_PROTECTED = 'write-protected';

    /**
     * {@inheritdoc}
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        ?UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.heroku.com/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://id.heroku.com/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://id.heroku.com/oauth/token');
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
        } elseif (isset($data['error_description']) || isset($data['error'])) {
            throw new TokenResponseException(
                sprintf(
                    'Error in retrieving token: "%s"',
                    $data['error_description'] ?? $data['error']
                )
            );
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifeTime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token'], $data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraOAuthHeaders()
    {
        return ['Accept' => 'application/vnd.heroku+json; version=3'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraApiHeaders()
    {
        return ['Accept' => 'application/vnd.heroku+json; version=3', 'Content-Type' => 'application/json'];
    }
}
