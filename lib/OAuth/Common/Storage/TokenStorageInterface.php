<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Storage;
use OAuth\Common\Token\TokenInterface;

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
