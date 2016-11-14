<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class Session implements TokenStorageInterface
{
    /**
     * @var bool
     */
    protected $startSession;

    /**
     * @var string
     */
    protected $sessionVariableName;

    /**
     * @var string
     */
    protected $stateVariableName;

    /**
     * @param bool $startSession Whether or not to start the session upon construction.
     * @param string $sessionVariableName the variable name to use within the _SESSION superglobal
     * @param string $stateVariableName
     */
    public function __construct(
        $startSession = true,
        $sessionVariableName = 'lusitanian-oauth-token',
        $stateVariableName = 'lusitanian-oauth-state'
    ) {
        if ($startSession && !$this->sessionHasStarted()) {
            session_start();
        }

        $this->startSession = $startSession;
        $this->sessionVariableName = $sessionVariableName;
        $this->stateVariableName = $stateVariableName;
        if (!isset($_SESSION[$sessionVariableName])) {
            $_SESSION[$sessionVariableName] = array();
        }
        if (!isset($_SESSION[$stateVariableName])) {
            $_SESSION[$stateVariableName] = array();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service, $account = null)
    {
        if ($this->hasAccessToken($service, $account)) {
            return unserialize($_SESSION[$this->sessionVariableName][$service.$account]);
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token, $account = null)
    {
        $serializedToken = serialize($token);

        if (isset($_SESSION[$this->sessionVariableName])
            && is_array($_SESSION[$this->sessionVariableName])
        ) {
            $_SESSION[$this->sessionVariableName][$service.$account] = $serializedToken;
        } else {
            $_SESSION[$this->sessionVariableName] = array(
                $service.$account => $serializedToken,
            );
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service, $account = null)
    {
        return isset($_SESSION[$this->sessionVariableName], $_SESSION[$this->sessionVariableName][$service.$account]);
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service, $account = null)
    {
        if (array_key_exists($service.$account, $_SESSION[$this->sessionVariableName])) {
            unset($_SESSION[$this->sessionVariableName][$service.$account]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        unset($_SESSION[$this->sessionVariableName]);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state, $account = null)
    {
        if (isset($_SESSION[$this->stateVariableName])
            && is_array($_SESSION[$this->stateVariableName])
        ) {
            $_SESSION[$this->stateVariableName][$service.$account] = $state;
        } else {
            $_SESSION[$this->stateVariableName] = array(
                $service.$account => $state,
            );
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service, $account = null)
    {
        return isset($_SESSION[$this->stateVariableName], $_SESSION[$this->stateVariableName][$service.$account]);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service, $account = null)
    {
        if ($this->hasAuthorizationState($service, $account)) {
            return $_SESSION[$this->stateVariableName][$service.$account];
        }

        throw new AuthorizationStateNotFoundException('State not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service, $account = null)
    {
        if (array_key_exists($service.$account, $_SESSION[$this->stateVariableName])) {
            unset($_SESSION[$this->stateVariableName][$service.$account]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        unset($_SESSION[$this->stateVariableName]);

        // allow chaining
        return $this;
    }

    public function __destruct()
    {
        if ($this->startSession) {
            session_write_close();
        }
    }

    /**
     * Determine if the session has started.
     * @url http://stackoverflow.com/a/18542272/1470961
     * @return bool
     */
    protected function sessionHasStarted()
    {
        // For more modern PHP versions we use a more reliable method.
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return session_status() != PHP_SESSION_NONE;
        }

        // Below PHP 5.4 we should test for the current session ID.
        return session_id() !== '';
    }
}
