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
 * Linkedin service.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 *
 * @see http://developer.linkedin.com/documents/authentication
 */
class Linkedin extends AbstractService
{
    /**
     * Defined scopes.
     *
     * @see https://docs.microsoft.com/en-us/linkedin/shared/authentication/authorization-code-flow?context=linkedin/context
     */
    const SCOPE_R_LITEPROFILE = 'r_liteprofile';
    const SCOPE_R_FULLPROFILE = 'r_fullprofile';
    const SCOPE_R_EMAILADDRESS = 'r_emailaddress';
    const SCOPE_R_NETWORK = 'r_network';
    const SCOPE_R_CONTACTINFO = 'r_contactinfo';
    const SCOPE_RW_NUS = 'rw_nus';
    const SCOPE_RW_COMPANY_ADMIN = 'rw_company_admin';
    const SCOPE_RW_GROUPS = 'rw_groups';
    const SCOPE_W_MESSAGES = 'w_messages';
    const SCOPE_W_MEMBER_SOCIAL = 'w_member_social';

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        ?UriInterface $baseApiUri = null
    ) {
        if (count($scopes) === 0) {
            $scopes = [self::SCOPE_R_LITEPROFILE, self::SCOPE_R_EMAILADDRESS];
        }

        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.linkedin.com/v2/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://www.linkedin.com/oauth/v2/authorization');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://www.linkedin.com/oauth/v2/accessToken');
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
        $token->setLifeTime($data['expires_in']);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token'], $data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }
}
