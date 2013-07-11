<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class Session implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $sessionVariableName;

    /**
     * @param bool $startSession Whether or not to start the session upon construction.
     * @param string $sessionVariableName the variable name to use within the _SESSION superglobal
     */
    public function __construct($startSession = true, $sessionVariableName = 'lusitanian_oauth_token')
    {
        if( $startSession && !isset($_SESSION)) {
            session_start();
        }

        $this->sessionVariableName = $sessionVariableName;
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) 
        {
            return $_SESSION[$this->sessionVariableName][$service];
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        if (isset($_SESSION[$this->sessionVariableName]) &&
            is_array($_SESSION[$this->sessionVariableName]))
        {
            $_SESSION[$this->sessionVariableName][$service] = $token;
        }
        else
        {
            $_SESSION[$this->sessionVariableName] = array(
                $service => $token,
            );
        }
        
        // allow chaining
        return $this;
    }

    /**
    * @return bool
    */
    public function hasAccessToken($service)
    {
        return isset($_SESSION[$this->sessionVariableName][$service]);
    }

    public function clearToken()
    {
        unset($_SESSION[$this->sessionVariableName]);

        // allow chaining
        return $this;
    }

    public function  __destruct()
    {
        session_write_close();
    }
}
