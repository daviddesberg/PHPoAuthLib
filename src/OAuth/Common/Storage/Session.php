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
    public function retrieveAccessToken()
    {
        if( isset( $_SESSION[$this->sessionVariableName] ) ) {
            return $_SESSION[$this->sessionVariableName];
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token)
    {
        $_SESSION[$this->sessionVariableName] = $token;
    }

    /**
    * @return bool
    */
    public function hasAccessToken()
    {
        return isset( $_SESSION[$this->sessionVariableName] );
    }

    public function clearToken()
    {
        unset($_SESSION[$this->sessionVariableName]);
    }

    public function  __destruct()
    {
        session_write_close();
    }
}
