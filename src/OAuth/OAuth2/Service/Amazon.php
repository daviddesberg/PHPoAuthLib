<?php

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Amazon service.
 *
 * @author Flávio Heleno <flaviohbatista@gmail.com>
 * @link https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
 */
class Amazon extends AbstractService
{
    /**
     * Defined scopes
     * @link https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
     */
    const SCOPE_PROFILE     = 'profile';
    const SCOPE_PROFILE_ID  = 'profile:user_id';
    const SCOPE_POSTAL_CODE = 'postal_code';
    /**
     * Defined scopes with Pay with Amazon service.
     * @link https://images-na.ssl-images-amazon.com/images/G/01/mwsportal/doc/en_US/offamazonpayments/LoginAndPayWithAmazonIntegrationGuide._V335378063_.pdf
     */
    const SCOPE_PAYMENTS_WIDGET = 'payments:widget';
    const SCOPE_PAYMENTS_SHIPPING_ADDRESS = 'payments:shipping_address';

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);

        if (null === $baseApiUri) {
            if (DEBUG || STAGING) {
                $this->baseApiUri = new Uri('https://api.sandbox.amazon.co.uk/');
            } else {
                $this->baseApiUri = new Uri('https://api.amazon.co.uk/');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        if (DEBUG || STAGING) {
            return new Uri('https://api.sandbox.amazon.co.uk/ap/oa');
        } else {
            return new Uri('https://api.amazon.co.uk/ap/oa');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        if (DEBUG || STAGING) {
            return new Uri('https://api.sandbox.amazon.co.uk/ap/oatoken');
        } else {
            return new Uri('https://api.amazon.co.uk/ap/oatoken');
        }
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
}
