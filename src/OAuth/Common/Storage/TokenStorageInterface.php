<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;

/**
 * All token storage providers must implement this interface.
 */
interface TokenStorageInterface
{
    /**
     * @param string $service
     * @param string $account
     *
     * @return TokenInterface
     *
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service, $account = null);

    /**
     * @param string         $service
     * @param string $account
     * @param TokenInterface $token
     *
     * @return TokenStorageInterface
     */
    public function storeAccessToken($service, $account = null, TokenInterface $token);

    /**
     * @param string $service
     * @param string $account
     *
     * @return bool
     */
    public function hasAccessToken($service, $account = null);

    /**
     * Delete the users token. Aka, log out.
     *
     * @param string $service
     * @param string $account
     *
     * @return TokenStorageInterface
     */
    public function clearToken($service, $account = null);

    /**
     * Delete *ALL* user tokens. Use with care. Most of the time you will likely
     * want to use clearToken() instead.
     *
     * @return TokenStorageInterface
     */
    public function clearAllTokens();

    /**
     * Store the authorization state related to a given service
     *
     * @param string $service
     * @param string $account
     * @param string $state
     *
     * @return TokenStorageInterface
     */
    public function storeAuthorizationState($service, $account = null, $state);

    /**
     * Check if an authorization state for a given service exists
     *
     * @param string $service
     * @param string $account
     *
     * @return bool
     */
    public function hasAuthorizationState($service, $account = null);

    /**
     * Retrieve the authorization state for a given service
     *
     * @param string $service
     * @param string $account
     *
     * @return string
     */
    public function retrieveAuthorizationState($service, $account = null);

    /**
     * Clear the authorization state of a given service
     *
     * @param string $service
     * @param string $account
     *
     * @return TokenStorageInterface
     */
    public function clearAuthorizationState($service, $account = null);

    /**
     * Delete *ALL* user authorization states. Use with care. Most of the time you will likely
     * want to use clearAuthorization() instead.
     *
     * @return TokenStorageInterface
     */
    public function clearAllAuthorizationStates();
}
