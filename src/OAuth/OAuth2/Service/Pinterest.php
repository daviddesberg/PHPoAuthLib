<?php
namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Exception\Exception;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Pinterest extends AbstractService
{
    /**
     * Pinterest www url - used to build dialog urls
     */
    const WWW_URL = 'https://www.pinterest.com/';

    /**
     * Pintrest API
     */
    const API_URL = 'https://api.pinterest.com';

    const SCOPE_READ_PUBLIC = 'read_public';
    const SCOPE_WRITE_PUBLIC = 'write_public';
    const SCOPE_READ_RELATIONSHIPS = 'read_relationships';
    const SCOPE_WRITE_RELATIONSHIPS = 'write_relationships';

    /** @var bool */
    protected $stateParameterInAuthUrl = true;

    /**
     * Available actions
     *
     * @var array
     */
    protected $_actions = [
        'follow',
        'unfollow',
        'block',
        'unblock',
        'approve',
        'deny',
    ];

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        UriInterface $baseApiUri = null,
        $apiVersion = ""
    ) {
        parent::__construct(
            $credentials, $httpClient, $storage, $scopes, $baseApiUri, false,
            $apiVersion
        );

        $this->apiVersion = "v1";
        if (is_null($baseApiUri)) {
            $this->baseApiUri = new Uri(
                self::API_URL .
                $this->getApiVersionString()
            );
        }
    }

    public function getDialogUri(array $parameters)
    {
        if (!isset($parameters['redirect_uri'])) {
            throw new Exception("Redirect uri is mandatory for this request");
        }
        $parameters['client_id'] = $this->credentials->getConsumerId();
        $baseUrl = self::API_URL . '/oauth/';
        $query = http_build_query($parameters);

        return new Uri($baseUrl . '?' . $query);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri(self::API_URL . '/oauth');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri . '/oauth/token');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);
        if (is_null($data) || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException(
                'Error in retrieving token: "' . $data['error'] . '"'
            );
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        $token->setLifetime(0);

        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token']);

        $token->setExtraParams($data);

        return $token;
    }
}
