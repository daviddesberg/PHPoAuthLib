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
    public function retrieveAccessToken();

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token);

    /**
     * @return bool
     */
    public function hasAccessToken();

    /**
    * Delete the users token. Aka, log out.
    */
    public function clearToken();
}
