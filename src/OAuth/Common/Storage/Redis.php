<?php

namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;
use Predis\Client as Predis;

/*
 * Stores a token in a Redis server. Requires the Predis library available at https://github.com/nrk/predis
 */
class Redis implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $key;

    protected $stateKey;

    /**
     * @var object|\Redis
     */
    protected $redis;

    /**
     * @var object|TokenInterface
     */
    protected $cachedTokens;

    /**
     * @var object
     */
    protected $cachedStates;

    /**
     * @param Predis $redis An instantiated and connected redis client
     * @param string $key The key to store the token under in redis
     * @param string $stateKey The key to store the state under in redis.
     */
    public function __construct(Predis $redis, $key, $stateKey)
    {
        $this->redis = $redis;
        $this->key = $key;
        $this->stateKey = $stateKey;
        $this->cachedTokens = array();
        $this->cachedStates = array();
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service, $account = null)
    {
        if (!$this->hasAccessToken($service, $account)) {
            throw new TokenNotFoundException('Token not found in redis');
        }

        if (isset($this->cachedTokens[$service.$account])) {
            return $this->cachedTokens[$service.$account];
        }

        $val = $this->redis->hget($this->key, $service.$account);

        return $this->cachedTokens[$service.$account] = unserialize($val);
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, $account = null, TokenInterface $token)
    {
        // (over)write the token
        $this->redis->hset($this->key, $service.$account, serialize($token));
        $this->cachedTokens[$service.$account] = $token;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service, $account = null)
    {
        if (isset($this->cachedTokens[$service.$account])
            && $this->cachedTokens[$service.$account] instanceof TokenInterface
        ) {
            return true;
        }

        return $this->redis->hexists($this->key, $service.$account);
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service, $account = null)
    {
        $this->redis->hdel($this->key, $service.$account);
        unset($this->cachedTokens[$service.$account]);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        // memory
        $this->cachedTokens = array();

        // redis
        $keys = $this->redis->hkeys($this->key);
        $me = $this; // 5.3 compat

        // pipeline for performance
        $this->redis->pipeline(
            function ($pipe) use ($keys, $me) {
                foreach ($keys as $k) {
                    $pipe->hdel($me->getKey(), $k);
                }
            }
        );

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service, $account = null)
    {
        if (!$this->hasAuthorizationState($service, $account)) {
            throw new AuthorizationStateNotFoundException('State not found in redis');
        }

        if (isset($this->cachedStates[$service.$account])) {
            return $this->cachedStates[$service.$account];
        }

        $val = $this->redis->hget($this->stateKey, $service.$account);

        return $this->cachedStates[$service.$account] = $val;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $account = null, $state)
    {
        // (over)write the token
        $this->redis->hset($this->stateKey, $service.$account, $state);
        $this->cachedStates[$service.$account] = $state;

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service, $account = null)
    {
        if (isset($this->cachedStates[$service.$account])
            && null !== $this->cachedStates[$service.$account]
        ) {
            return true;
        }

        return $this->redis->hexists($this->stateKey, $service.$account);
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service, $account = null)
    {
        $this->redis->hdel($this->stateKey, $service.$account);
        unset($this->cachedStates[$service.$account]);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        // memory
        $this->cachedStates = array();

        // redis
        $keys = $this->redis->hkeys($this->stateKey);
        $me = $this; // 5.3 compat

        // pipeline for performance
        $this->redis->pipeline(
            function ($pipe) use ($keys, $me) {
                foreach ($keys as $k) {
                    $pipe->hdel($me->getKey(), $k);
                }
            }
        );

        // allow chaining
        return $this;
    }

    /**
     * @return Predis $redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }
}
