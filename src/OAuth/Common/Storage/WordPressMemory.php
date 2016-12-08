<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;

/*
 * Stores a token in WordPress options database table
 */
class WordPressMemory implements TokenStorageInterface
{
    /**
     * @var object|TokenInterface
     */
    protected $tokens;

    /**
     * Key for storing token in WordPress options table
     * @var string
     */
    private $tokenOptionName;

    /**
     * Key for storing state in WordPress options table
     * @var string
     */
    private $stateOptionName;

    /**
     * @var array
     */
    protected $states;

    public function __construct()
    {
        $this->tokenOptionName = 'lusitanian_oauth_token';
        $this->stateOptionName = 'lusitanian_oauth_state';

        $this->tokens = (array)maybe_unserialize( get_option( $this->tokenOptionName ) );
        $this->states = (array)maybe_unserialize( get_option( $this->stateOptionName ) );
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
            return $this->tokens[$service];
        }

        throw new TokenNotFoundException('Token not stored');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {

        $this->tokens[$service] = $token;

        update_option( $this->tokenOptionName, $this->tokens);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        return is_array($this->tokens) && isset($this->tokens[$service]) && $this->tokens[$service] instanceof TokenInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        if (array_key_exists($service, $this->tokens)) {
            unset($this->tokens[$service]);
        }

        update_option( $this->tokenOptionName, $this->tokens);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {

        $this->tokens = array();

        update_option( $this->tokenOptionName, $this->tokens);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        if ($this->hasAuthorizationState($service)) {
            return $this->states[$service];
        }

        throw new AuthorizationStateNotFoundException('State not stored');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $this->states[$service] = $state;

        update_option( $this->stateOptionName, $this->states);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
        return isset($this->states[$service]) && null !== $this->states[$service];
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
        if (array_key_exists($service, $this->states)) {
            unset($this->states[$service]);

            update_option( $this->stateOptionName, $this->states);
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

        update_option( $this->stateOptionName, $this->states);

        // allow chaining
        return $this;
    }
}
