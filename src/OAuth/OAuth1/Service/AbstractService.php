<?php
/**
 * @author David Desberg <david@thedesbergs.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Exception\InvalidTokenResponseException;
use OAuth\Common\Exception\InvalidScopeException;
use OAuth\Common\Exception\MissingRefreshTokenException;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Service\ServiceInterface;

/**
 * Abstract service for OAuth1.
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\Common\Token\TokenInterface
     * @param string $responseBody
     */
    abstract protected function parseAccessTokenResponse($responseBody);
}
