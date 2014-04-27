<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;
use Illuminate\Session\Store;

/**
 * Stores a token in a PHP session.
 */
class LaravelSession implements TokenStorageInterface
{
    /**
     * @var object|Store
     */
    protected $session;

    /**
     * @var string
     */
    protected $sessionVariableName;

    /**
     * @var string
     */
    protected $stateVariableName;

    /**
     * @param string $sessionVariableName
     * @param string $stateVariableName
     */
    public function __construct(
        Store $session,
        $sessionVariableName = 'lusitanian_oauth_token',
        $stateVariableName = 'lusitanian_oauth_state'
    ) {
        $this->session = $session;
        $this->sessionVariableName = $sessionVariableName;
        $this->stateVariableName = $stateVariableName;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
            return unserialize($this->session->get("$this->sessionVariableName.$service"));
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $this->session->set("$this->sessionVariableName.$service", serialize($token));

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        return $this->session->has("$this->sessionVariableName.$service");
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        $this->session->forget("$this->sessionVariableName.$service");

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        $this->session->forget($this->sessionVariableName);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $this->session->set("$this->stateVariableName.$service", $state);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
        return $this->session->has("$this->stateVariableName.$service");
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        if ($this->hasAuthorizationState($service)) {
            return $this->session->get("$this->stateVariableName.$service");
        }

        throw new AuthorizationStateNotFoundException('State not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
        $this->session->forget("$this->stateVariableName.$service");

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        $this->session->forget($this->stateVariableName);

        // allow chaining
        return $this;
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }
}
