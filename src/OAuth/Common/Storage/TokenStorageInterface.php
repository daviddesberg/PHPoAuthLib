<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;

/**
 * All token storage providers must implement this interface.
 */
interface TokenStorageInterface
{
    /**
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function retrieveAccessToken($service);

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken($service, TokenInterface $token);

    /**
     * @return bool
     */
    public function hasAccessToken($service);

    /**
    * Delete the users token. Aka, log out.
    */
    public function clearToken();
}
