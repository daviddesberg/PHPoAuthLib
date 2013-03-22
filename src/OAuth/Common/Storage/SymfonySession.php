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

    public function retrieveAccessToken()
    {
        if ($this->session->has($this->sessionVariableName)) {
            return $this->session->get($this->sessionVariableName);
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    public function storeAccessToken(TokenInterface $token)
    {
        $this->session->set($this->sessionVariableName, $token);
    }

    /**
    * @return bool
    */
    public function hasAccessToken()
    {
        return $this->session->has($this->sessionVariableName);
    }

    /**
    * Delete the users token. Aka, log out.
    */
    public function clearToken()
    {
        $this->session->remove($this->sessionVariableName);
    }
}
