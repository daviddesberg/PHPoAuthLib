<?php

namespace OAuth\Common\Storage;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class Doctrine implements TokenStorageInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $stateKey;

    /**
     * @param CacheProvider $cache
     * @param string        $key
     * @param string        $stateKey
     */
    public function __construct(CacheProvider $cache, $key, $stateKey)
    {
        $this->cache = $cache;
        $this->key = $key;
        $this->stateKey = $stateKey;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        if ($token = $this->cache->fetch($this->key . $service)) {
            return $token;
        }

        throw new TokenNotFoundException('Token not found in cache, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $this->cache->save($this->key . $service, $token);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        return $this->cache->contains($this->key . $service);
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        $this->cache->delete($this->key . $service);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        $this->cache->flushAll();

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $this->cache->save($this->stateKey . $service, $state);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
        return $this->cache->contains($this->stateKey . $service);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        if ($state = $this->cache->fetch($this->stateKey . $service)) {
            return $state;
        }

        throw new AuthorizationStateNotFoundException('State not found in cache, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
        $this->cache->delete($this->stateKey . $service);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        $this->cache->flushAll();

        // allow chaining
        return $this;
    }
}
