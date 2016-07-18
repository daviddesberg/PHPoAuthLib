<?php

namespace OAuth\OAuth1\Service;

use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\SignatureInterface;

class QuickBooks extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        UriInterface $baseApiUri = null
    ) {
        parent::__construct(
            $credentials,
            $httpClient,
            $storage,
            $signature,
            $baseApiUri
        );

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://quickbooks.api.intuit.com/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint()
    {
        return new Uri('https://oauth.intuit.com/oauth/v1/get_request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://appcenter.intuit.com/Connect/Begin');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://oauth.intuit.com/oauth/v1/get_access_token');
    }

    /**
     * {@inheritDoc}
     */
    public function request(
        $path,
        $method = 'GET',
        $body = null,
        array $extraHeaders = array()
    ) {
        $extraHeaders['Accept'] = 'application/json';
        return parent::request($path, $method, $body, $extraHeaders);
    }
}
