<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Token
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Common\Token;

/**
 * Base token interface for any OAuth version.
 */
interface TokenInterface
{
    /**
     * Denotes an unknown end of life time.
     */
    const EOL_UNKNOWN = -9001;

    /**
     * Denotes a token which never expires, should only happen in OAuth1.
     */
    const EOL_NEVER_EXPIRES = -9002;

    public function __construct($accessToken = null, $refreshToken = null, $lifetime = null, $extraParams = array() );

    /**
     * @abstract
     * @return string
     */
    public function getAccessToken();

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
