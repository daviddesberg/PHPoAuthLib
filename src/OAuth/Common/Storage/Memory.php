<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Token\TokenInterface;

// Stores a token in-memory only (destroyed at end of script execution).
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
        $this->tokens = [];
        $this->states = [];
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
            return $this->tokens[$service];
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * {@inheritdoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $this->tokens[$service] = $token;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAccessToken($service)
    {
        return isset($this->tokens[$service]) && $this->tokens[$service] instanceof TokenInterface;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function clearAllTokens()
    {
        $this->tokens = [];

        // allow chaining
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveAuthorizationState($service)
    {
        if ($this->hasAuthorizationState($service)) {
            return $this->states[$service];
        }

        throw new AuthorizationStateNotFoundException('State not stored');
    }

    /**
     * {@inheritdoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $this->states[$service] = $state;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAuthorizationState($service)
    {
        return isset($this->states[$service]) && null !== $this->states[$service];
    }

    /**
     * {@inheritdoc}
     */
    public function clearAuthorizationState($service)
    {
        if (array_key_exists($service, $this->states)) {
            unset($this->states[$service]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAllAuthorizationStates()
    {
        $this->states = [];

        // allow chaining
        return $this;
    }
}
