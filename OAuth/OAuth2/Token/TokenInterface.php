<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Token;
use OAuth\Common\Token\TokenInterface as BaseTokenInterface;

/**
 * OAuth2 specific TokenInterface
 */
interface TokenInterface extends BaseTokenInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getRefreshToken();

    /**
     * @abstract
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken);
}
