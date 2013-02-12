<?php
namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Service\ServiceInterface as BaseServiceInterface;
use OAuth\OAuth1\Signature\SignatureInterface;

/**
 * Defines the common methods across OAuth 1 services.
 */
interface ServiceInterface extends BaseServiceInterface
{
    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\Client\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param \OAuth\OAuth1\Signature\SignatureInterface $signature
     * @param UriInterface|null $baseApiUri
     * @abstract
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, SignatureInterface $signature, UriInterface $baseApiUri = null);

    /**
     * Retrieves and stores/returns the OAuth1 request token.
     *
     * @abstract
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestRequestToken();

    /**
     * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
     *
     * @abstract
     * @param string $token The request token from the callback.
     * @param string $verifier
     * @param string $tokenSecret
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestAccessToken($token, $verifier, $tokenSecret);

    /**
     * @abstract
     * @return UriInterface
     */
    public function getRequestTokenEndpoint();
}
