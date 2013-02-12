<?php
namespace OAuth\OAuth1\Token;

use OAuth\Common\Token\TokenInterface as BaseTokenInterface;

/**
 * OAuth1 specific token interface
 */
interface TokenInterface extends BaseTokenInterface
{
    /**
     * @return string
     */
    public function getAccessTokenSecret();

    /**
     * @param string $accessTokenSecret
     */
    public function setAccessTokenSecret($accessTokenSecret);

    /**
     * @return string
     */
    function getRequestTokenSecret();

    /**
     * @param string $requestTokenSecret
     */
    function setRequestTokenSecret($requestTokenSecret);

    /**
     * @return string
     */
    function getRequestToken();

    /**
     * @param string $requestToken
     */
    function setRequestToken($requestToken);
}
