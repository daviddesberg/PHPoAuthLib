<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth2\Token;

interface TokenInterface
{
    public function __construct($accessToken = null, $refreshToken = null, $lifetime = null, $extraParams = array() );

    /**
     * @abstract
     * @return string
     */
    public function getAccessToken();

    /**
     * @abstract
     * @return string
     */
    public function getRefreshToken();

    /**
     * @abstract
     * @return int
     */
    public function getEndOfLife();

    /**
     * @abstract
     * @return array
     */
    public function getExtraParams();

    /**
     * @abstract
     * @param $accessToken
     */
    public function setAccessToken($accessToken);

    /**
     * @abstract
     * @param $refreshToken
     */
    public function setRefreshToken($refreshToken);

    /**
     * @abstract
     */
    public function setEndOfLife($endOfLife);

    /**
     * @abstract
     * @param $lifetime
     */
    public function setLifetime($lifetime);

    /**
     * @abstract
     * @param array $extraParams
     */
    public function setExtraParams(array $extraParams);


}
