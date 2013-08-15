<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;

/**
 * All token storage providers must implement this interface.
 */
interface TokenStorageInterface
{
    /**
     * @param string $service
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function retrieveAccessToken($service);

    /**
     * @param string $service
     * @param \OAuth\Common\Token\TokenInterface $token
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function storeAccessToken($service, TokenInterface $token);

    /**
     * @param string $service
     * @return bool
     */
    public function hasAccessToken($service);

    /**
     * Delete the users token. Aka, log out.
     *
     * @param string $service
     * @return TokenStorageInterface
     */
    public function clearToken($service);

    /**
     * Delete *ALL* user tokens.
     *
     * @return TokenStorageInterface
     */
    public function clearAllTokens();
}
