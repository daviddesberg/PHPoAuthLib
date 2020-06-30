<?php

namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Pocket extends AbstractService
{
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        ?UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if ($baseApiUri === null) {
            $this->baseApiUri = new Uri('https://getpocket.com/v3/');
        }
    }

    public function getRequestTokenEndpoint()
    {
        return new Uri('https://getpocket.com/v3/oauth/request');
    }

    public function getAuthorizationEndpoint()
    {
        return new Uri('https://getpocket.com/auth/authorize');
    }

    public function getAccessTokenEndpoint()
    {
        return new Uri('https://getpocket.com/v3/oauth/authorize');
    }

    public function getAuthorizationUri(array $additionalParameters = [])
    {
        $parameters = array_merge(
            $additionalParameters,
            [
                'redirect_uri' => $this->credentials->getCallbackUrl(),
            ]
        );

        // Build the url
        $url = clone $this->getAuthorizationEndpoint();
        foreach ($parameters as $key => $val) {
            $url->addToQuery($key, $val);
        }

        return $url;
    }

    public function requestRequestToken()
    {
        $responseBody = $this->httpClient->retrieveResponse(
            $this->getRequestTokenEndpoint(),
            [
                'consumer_key' => $this->credentials->getConsumerId(),
                'redirect_uri' => $this->credentials->getCallbackUrl(),
            ]
        );

        $code = $this->parseRequestTokenResponse($responseBody);

        return $code;
    }

    protected function parseRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data['code'])) {
            throw new TokenResponseException('Error in retrieving code.');
        }

        return $data['code'];
    }

    public function requestAccessToken($code, $state = null)
    {
        $bodyParams = [
            'consumer_key' => $this->credentials->getConsumerId(),
            'code' => $code,
        ];

        $responseBody = $this->httpClient->retrieveResponse(
            $this->getAccessTokenEndpoint(),
            $bodyParams,
            $this->getExtraOAuthHeaders()
        );
        $token = $this->parseAccessTokenResponse($responseBody);
        $this->storage->storeAccessToken($this->service(), $token);

        return $token;
    }

    protected function parseAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if ($data === null || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        //$token->setRequestToken($data['access_token']);
        $token->setAccessToken($data['access_token']);
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data['access_token']);
        $token->setExtraParams($data);

        return $token;
    }
}
