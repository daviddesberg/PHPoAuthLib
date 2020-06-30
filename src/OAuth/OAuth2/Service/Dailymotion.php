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
 * Dailymotion service.
 *
 * @author Mouhamed SEYE <mouhamed@seye.pro>
 *
 * @see http://www.dailymotion.com/doc/api/authentication.html
 */
class Dailymotion extends AbstractService
{
    /**
     * Scopes.
     *
     * @var string
     */
    const SCOPE_EMAIL = 'email';
    const SCOPE_PROFILE = 'userinfo';
    const SCOPE_VIDEOS = 'manage_videos';
    const SCOPE_COMMENTS = 'manage_comments';
    const SCOPE_PLAYLIST = 'manage_playlists';
    const SCOPE_TILES = 'manage_tiles';
    const SCOPE_SUBSCRIPTIONS = 'manage_subscriptions';
    const SCOPE_FRIENDS = 'manage_friends';
    const SCOPE_FAVORITES = 'manage_favorites';
    const SCOPE_GROUPS = 'manage_groups';

    /**
     * Dialog form factors.
     *
     * @var string
     */
    const DISPLAY_PAGE = 'page';
    const DISPLAY_POPUP = 'popup';
    const DISPLAY_MOBILE = 'mobile';

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
            $this->baseApiUri = new Uri('https://api.dailymotion.com/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.dailymotion.com/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.dailymotion.com/oauth/token');
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
        return ['Accept' => 'application/json'];
    }
}
