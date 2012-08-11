<?php
/**
 * Builds a signature to sign OAuth1 requests
 *
 * PHP version 5.4
 *
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
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
     * @param mixed $requestBody
     * @param array $authorizationHeader
     * @param string $method
     * @abstract
     */
    public function getSignature(UriInterface $uri, $requestBody = null, array $authorizationHeader = [], $method = 'POST');
}