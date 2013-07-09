<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SymfonySession implements TokenStorageInterface
{
    private $session;
    private $sessionVariableName;

    public function __construct(SessionInterface $session, $startSession = true, $sessionVariableName = 'lusitanian_oauth_token')
    {
        $this->session = $session;
        $this->sessionVariableName = $sessionVariableName;
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken($service)
    {
        if ($this->hasAccessToken($service)) {
            // get from session
            $tokens = $this->session->get($this->sessionVariableName);

            // one item
            return $tokens[$service];
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    public function storeAccessToken($service, TokenInterface $token)
    {
        // get previously saved tokens
        $tokens = $this->session->get($this->sessionVariableName);

        if (is_array($tokens)) 
        {
            // add to array
            $tokens[$service] = $token;
        }
        else
        {
            // new array
            $tokens = array(
                $service => $token,
            );
        }

        // save
        $this->session->set($this->sessionVariableName, $tokens);

        // allow chaining
        return $this;
    }

    /**
    * @return bool
    */
    public function hasAccessToken($service)
    {
        // get from session
        $tokens = $this->session->get($this->sessionVariableName);

        return is_array($tokens) &&
               isset($tokens[$service]) &&
               $tokens[$service] instanceof TokenInterface;
    }

    /**
    * Delete the users tokens. Aka, log out.
    */
    public function clearToken()
    {
        $this->session->remove($this->sessionVariableName);

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
