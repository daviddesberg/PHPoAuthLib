<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/*
 * Stores a token in-memory only (destroyed at end of script execution).
 */
class Memory implements TokenStorageInterface
{
    /**
     * @var object|TokenInterface
     */
    protected $tokens;

    /**
     * @var array
     */
    protected $states;

    public function __construct()
    {
        $this->tokens = array();
        $this->states = array();
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service, $account = null)
    {
        if ($this->hasAccessToken($service, $account)) {
            return $this->tokens[$service.$account];
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, $account = null, TokenInterface $token)
    {
        $this->tokens[$service.$account] = $token;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service, $account = null)
    {
        return isset($this->tokens[$service.$account]) && $this->tokens[$service.$account] instanceof TokenInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service, $account = null)
    {
        if (array_key_exists($service.$account, $this->tokens)) {
            unset($this->tokens[$service.$account]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        $this->tokens = array();

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service, $account = null)
    {
        if ($this->hasAuthorizationState($service, $account)) {
            return $this->states[$service.$account];
        }

        throw new AuthorizationStateNotFoundException('State not stored');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $account = null, $state)
    {
        $this->states[$service.$account] = $state;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service, $account = null)
    {
        return isset($this->states[$service.$account]) && null !== $this->states[$service.$account];
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service, $account = null)
    {
        if (array_key_exists($service.$account, $this->states)) {
            unset($this->states[$service.$account]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        $this->states = array();

        // allow chaining
        return $this;
    }
}
