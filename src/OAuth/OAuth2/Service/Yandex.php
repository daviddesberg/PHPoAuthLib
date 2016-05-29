<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Service\Exception\InvalidAccessTypeException;
use OAuth\Common\Http\Uri\Uri;

class Yandex extends AbstractService {


    public function __construct(
      CredentialsInterface $credentials,
      ClientInterface $httpClient,
      TokenStorageInterface $storage,
      $scopes = array(),
      UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://login.yandex.ru/info/');
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
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);

        unset($data['access_token']);

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
        return new Uri('https://oauth.yandex.ru/authorize');
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://oauth.yandex.ru/token');
    }


    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUri(array $additionalParameters = array())
    {
        $parameters = array_merge(
          $additionalParameters,
          array(
            'client_id'     => $this->credentials->getConsumerId(),
            'response_type' => 'code',
          )
        );
        
        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($parameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function requestAccessToken($code, $state = null)
    {
        if (null !== $state) {
            $this->validateAuthorizationState($state);
        }

        $bodyParams = array(
          'grant_type'    => 'authorization_code',
          'code'          => $code,
          'client_id'     => $this->credentials->getConsumerId(),
          'client_secret' => $this->credentials->getConsumerSecret(),
        );

        $responseBody = $this->httpClient->retrieveResponse(
          $this->getAccessTokenEndpoint(),
          $bodyParams,
          $this->getExtraOAuthHeaders(),
          'POST'
        );

        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }


}