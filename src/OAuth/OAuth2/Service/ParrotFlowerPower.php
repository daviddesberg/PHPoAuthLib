<?php
/**
 * ParrotFlowerPower service.
 *
 * @author  Pedro Ammorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    https://flowerpowerdev.parrot.com/projects/flower-power-web-service-api/wiki
 */

namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * ParrotFlowerPower service.
 *
 * @author  Pedro Ammorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    https://flowerpowerdev.parrot.com/projects/flower-power-web-service-api/wiki
 */
class ParrotFlowerPower extends AbstractService
{

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = array(),
        UriInterface $baseApiUri = null
    ) {
        parent::__construct(
            $credentials,
            $httpClient,
            $storage,
            $scopes,
            $baseApiUri,
            true
        );

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://apiflowerpower.parrot.com/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->baseApiUri.'oauth2/v1/authorize');

    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri.'user/v1/authenticate');
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
            throw new TokenResponseException(
                'Error in retrieving token: "' . $data['error'] . '"'
            );
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
}
