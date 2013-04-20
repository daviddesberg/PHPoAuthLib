<?php
namespace OAuth\OAuth1\Signature;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\UriInterface;

interface SignatureInterface
{
    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @abstract
     */
    public function __construct(Credentials $credentials);

    /**
     * @param string $algorithm
     * @abstract
     */
    public function setHashingAlgorithm($algorithm);

    /**
     * @param string $token
     * @abstract
     */
    public function setTokenSecret($token);

    /**
     * @param \OAuth\Common\Http\Uri\UriInterface $uri
     * @param array $params
     * @param string $method
     * @abstract
     */
    public function getSignature(UriInterface $uri, array $params, $method = 'POST');
}
