<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;

/**
 * All token storage providers must implement this interface.
 */
interface TokenStorageInterface
{
    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token);

    /**
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function retrieveAccessToken();
}
