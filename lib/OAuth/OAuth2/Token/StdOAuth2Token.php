<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Token;

class StdOAuth2Token implements TokenInterface
{
    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $refreshToken;

    /**
     * @var int
     */
    protected $endOfLife;

    /**
     * @var array
     */
    protected $extraParams = [];

    /**
     * @param string $accessToken
     * @param string $refreshToken
     * @param int $lifetime
     * @param array $extraParams
     */
    public function __construct($accessToken = null, $refreshToken = null, $lifetime = null, $extraParams = [] )
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->setLifetime($lifetime);

        $this->extraParams = $extraParams;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */
    public function getEndOfLife()
    {
        return $this->endOfLife;
    }

    /**
     * @param array $extraParams
     */
    public function setExtraParams(array $extraParams)
    {
        $this->extraParams = $extraParams;
    }

    /**
     * @return array
     */
    public function getExtraParams()
    {
        return $this->extraParams;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param int $endOfLife
     */
    public function setEndOfLife($endOfLife)
    {
        $this->endOfLife = $endOfLife;
    }

    /**
     * @param $lifetime
     */
    public function setLifetime($lifetime)
    {
        /**
         * @todo verify that this is the correct behavior
         */
        if( 0 === $lifetime || static::EOL_NEVER_EXPIRES === $lifetime ) {
            $this->endOfLife = static::EOL_NEVER_EXPIRES;
        } elseif ( null !== $lifetime )  {
            $this->endOfLife = intval($lifetime) + time();
        } else {
            $this->endOfLife = static::EOL_UNKNOWN;
        }
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }
}
