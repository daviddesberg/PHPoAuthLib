<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Storage;
use OAuth\Common\Exception\TokenNotFoundException;
use OAuth\Common\Token\TokenInterface;

/**
 * Storage interface useful if user just wants to take the token from the service class methods and not have it stored automagically
 */
class Null implements TokenStorageInterface
{
    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token)
    {
        return;
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function retrieveAccessToken()
    {
        throw new TokenNotFoundException('Token cannot be stored in "Null" storage interface.');
    }

}
