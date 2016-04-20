<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Exception\Exception;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class Dataporten extends AbstractService
{
    // Authentication and userinfo
    const SCOPE_USER_ID                 = 'userid';
    const SCOPE_FEIDE_IDENTIFIER        = 'userid-feide';
    const SCOPE_NAME_PROFILE_PHOTO      = 'profile';
    const SCOPE_EMAIL                   = 'email';
    const SCOPE_NATIONAL_ID_NUMBER      = 'userid_nin'; // hidden
    const SCOPE_OPENID_ACCESS           = 'openid'; // tech
    const SCOPE_LONG_TERM_ACCESS        = 'longterm'; // tech

    // Groups
    const SCOPE_GROUPS                  = 'groups'; // See API docs
    const SCOPE_MEMBER_IDS              = 'groups-memberids'; // hidden
    const SCOPE_ORGADMIN_GROUPS         = 'groups-orgadmin'; // hidden, internal
    const SCOPE_PEOPLE_SEARCH           = 'peoplesearch'; // hidden


    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);
        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://auth.dataporten.no');
        }
    }

    /**
     * @inheritDoc
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

        if (isset($data['expires_in'])) {
            $token->setLifeTime($data['expires_in']);
        }

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://auth.dataporten.no/oauth/authorization');
    }

    /**
     * @inheritDoc
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://auth.dataporten.no/oauth/token');
    }

    /**
     * Used to configure response type -- we want JSON from Dataporten, default is query string format
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return array('Accept' => 'application/json');
    }

    /**
     * {@inheritdoc}
     */
    protected function getScopesDelimiter()
    {
        return ',';
    }

}
