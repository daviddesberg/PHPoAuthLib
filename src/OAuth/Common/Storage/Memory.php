<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;

/*
 * Stores a token in-memory only (destroyed at end of script execution).
 */
class Memory implements TokenStorageInterface
{
    /**
     * @var object|TokenInterface
     */
    protected $token;

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken()
    {
        if( $this->token instanceOf TokenInterface ) {
            return $this->token;
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token)
    {
        $this->token = $token;
    }

    /**
    * @return bool
    */
    public function hasAccessToken()
    {
        return $this->token instanceOf TokenInterface;
    }

    /**
    * Delete the users token. Aka, log out.
    */
    public function clearToken()
    {
        $this->token = null;
    }
}
