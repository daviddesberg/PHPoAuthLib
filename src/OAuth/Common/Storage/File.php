<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/*
 * Stores a token in a file
 */
class File implements TokenStorageInterface
{
    /**
     * @var array of object|TokenInterface
     */
    protected $tokens;

    /**
     * @var array
     */
    protected $states;

    /**
     * @var string
     */
    protected $file_path;

    public function __construct($file_path = null)
    {
        $this->tokens = array();
        $this->states = array();

        $this->file_path = $file_path;

        if($this->file_path != null)
        {
            if(file_exist($this->file_path))
            {
                $this->parseFromFile();
            }
            else
            {
                throw new TokenNotFoundException('Token file not existing.');
            }
        }
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

        $this->updateFile();

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        return isset($this->tokens[$service]) && $this->tokens[$service] instanceof TokenInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        if (array_key_exists($service, $this->tokens)) {
            unset($this->tokens[$service]);

            $this->updateFile();
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

        $this->updateFile();

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

        $this->updateFile();

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

            $this->updateFile();
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

        $this->updateFile();

        // allow chaining
        return $this;
    }

    /**
     * Update file containing tokens and states
     */
    private function updateFile()
    {
        $data = array(
            'tokens' => $this->tokens,
            'states' => $this->states
        );

        file_put_contents($this->file_path, serialize($data));
    }

    /**
     * Set file path
     * @param $file_path
     */
    protected function setFilePath($file_path) {
        $this->file_path = $file_path;
    }

    /**
     * Get serialized content from a file
     */
    private function parseFromFile()
    {
        $data = unserialize(file_get_contents($this->file_path));

        $this->tokens = $data['tokens'];
        $this->states = $data['states'];
    }
}
