<?php

namespace OAuthTest\Mocks\OAuth1\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\AbstractService;
use OAuth\OAuth1\Signature\SignatureInterface;

class Fake extends AbstractService
{
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        ?UriInterface $baseApiUri = null
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function parseRequestTokenResponse($responseBody): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody): void
    {
    }
}
