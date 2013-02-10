<?php
/**
 * OAuth 2 service interface.
 *
 * PHP Version 5.4
 *
 *
 * @author David Desberg <david@daviddesberg.com>
 *
 * @category   OAuth
 * @package    OAuth2
 * @subpackage Service
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */


namespace OAuth\OAuth2\Service;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Service\ServiceInterface as BaseServiceInterface;
use OAuth\Common\Http\Uri\UriInterface;
/**
 * Defines the common methods across OAuth 2 services.
 */
interface ServiceInterface extends BaseServiceInterface
{
    /**
     * Authorization methods for various services
     */
    const AUTHORIZATION_METHOD_HEADER_OAUTH = 0;
    const AUTHORIZATION_METHOD_HEADER_BEARER = 1;
    const AUTHORIZATION_METHOD_QUERY_STRING = 2;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Http\Client\ClientInterface $httpClient
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param array $scopes
     * @param UriInterface|null $baseApiUri
     * @abstract
     */
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = [], UriInterface $baseApiUri = null);

    /**
     * Retrieves and stores/returns the OAuth2 access token after a successful authorization.
     *
     * @abstract
     * @param string $code The access code from the callback.
     * @return TokenInterface $token
     * @throws TokenResponseException
     */
    public function requestAccessToken($code);
}
