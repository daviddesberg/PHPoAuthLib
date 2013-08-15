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
    protected $tokens;

    public function __construct()
    {
        $this->tokens = array();
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service))
        {
            return $this->tokens[$service];
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $this->tokens[$service] = $token;

        // allow chaining
        return $this;
    }

    /**
    * @return bool
    */
    public function hasAccessToken($service)
    {
        return isset($this->tokens[$service]) &&
               $this->tokens[$service] instanceOf TokenInterface;
    }

    /**
     * Delete the user's token. Aka, log out.
     */
    public function clearToken($service)
    {
        if (array_key_exists($service, $this->tokens)) {
            unset($this->tokens[$service]);
        }

        // allow chaining
        return $this;
    }

    /**
     * Delete *ALL* user tokens. Use with care. Most of the time you will likely
     * want to use clearToken() instead.
     */
    public function clearAllTokens()
    {
        $this->tokens = array();

        // allow chaining
        return $this;
    }
}
