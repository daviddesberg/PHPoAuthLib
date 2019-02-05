<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Exception\TokenResponseException;

/**
 * MYOB Service
 * @link http://developer.myob.com/api/accountright/api-overview/authentication/
 */
class Myob extends AbstractService
{
    /**
     * @var string The CompanyFile scope
     */
    const SCOPE_COMPANYFILE = "CompanyFile";

    /**
     * @inheritdoc
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri("https://secure.myob.com/oauth2/account/authorize");
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri("https://secure.myob.com/oauth2/v1/authorize");
    }

    /**
     * @inheritdoc
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error_description'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error_description'] . '"');
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

        unset($data['access_token']);
        unset($data['expires_in']);

        $token->setExtraParams($data);

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function requestAccessToken($code, $state = null)
    {
        if (null !== $state) {
            $this->validateAuthorizationState($state);
        }

        $bodyParams = array(
            'code'          => $code,
            'client_id'     => $this->credentials->getConsumerId(),
            'client_secret' => $this->credentials->getConsumerSecret(),
            'redirect_uri'  => $this->credentials->getCallbackUrl(),
            'grant_type'    => 'authorization_code',
            'scope'         => implode(' ', $this->scopes),
        );

        $responseBody = $this->httpClient->retrieveResponse(
            $this->getAccessTokenEndpoint(),
            $bodyParams,
            $this->getExtraOAuthHeaders()
        );

        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }
}