<?php


namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;

/**
 * Class Azure
 * @package OAuth\OAuth2\Service
 * @author Juan Diaz - FuriosoJack <iam@furiosojack.com>
 */
class MicrosoftGraph extends AbstractService
{
    private $resources = array(
        'graph' => 'https://graph.microsoft.com', // https://graph.windows.net
    );
    public function __construct(CredentialsInterface $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, array $scopes = array(), UriInterface $baseApiUri = null, $stateParameterInAutUrl = false, $apiVersion = "")
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, $stateParameterInAutUrl, $apiVersion);
        if (is_null($baseApiUri)) {
            $this->baseApiUri = new Uri($this->resources['graph'].'myorganization/');
        }
    }

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     *
     * @throws TokenResponseException
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
     * Returns the authorization API endpoint.
     *
     * @return UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://login.microsoftonline.com/common/oauth2/authorize');
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://login.microsoftonline.com/common/oauth2/token');
    }

    /**
     * @param $code
     * @param null $state
     * @return TokenInterface
     * @throws Exception\InvalidAuthorizationStateException
     * @throws TokenResponseException
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
            'resource' => $this->resources['graph'],
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

    /**
     * Devuelve los heades que tendra obtener el AccessToken
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return array(
            'Content-Type'=>'application/x-www-form-urlencoded',
        );
    }

    /**
     * Returns a class constant from ServiceInterface define el metodo e autorizacion del API
     * Header is the sane default.
     *
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }
}
