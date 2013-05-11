<?php
namespace OAuth\Common\Storage;

use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\StorageException;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use Redis;

/*
 * Stores a token in a Redis server. Requires the PHPRedis extension available at https://github.com/nicolasff/phpredis/
 */
class PHPRedis implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var object|\Redis
     */
    protected $redis;

    /**
     * @var object|TokenInterface
     */
    protected $cachedToken;

    /**
     * @param \Redis $redis An instantiated and connected redis client
     * @param string $key The key to store the token under in redis.
     */
    public function __construct(Redis $redis, $key)
    {
        $this->redis = $redis;
        $this->key = $key;
    }

    /**
     * @return \OAuth\Common\Token\TokenInterface
     * @throws TokenNotFoundException
     */
    public function retrieveAccessToken()
    {
        if( $this->cachedToken ) {
            return $this->cachedToken;
        }

        $val = $this->redis->get( $this->key );
        if( false === $val ) {
            throw new TokenNotFoundException('Token not found in redis');
        }

        $this->cachedToken = unserialize( $val );
        return $this->cachedToken;
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     * @throws StorageException
     */
    public function storeAccessToken(TokenInterface $token)
    {
        // let redis exceptions bubble up
        if( $this->redis->set($this->key, serialize($token)) ) {
            $this->cachedToken = $token;
            return;
        }

        throw new StorageException('Unable to store token');
    }

    /**
    * @return bool
    */
    public function hasAccessToken()
    {
        if( $this->cachedToken ) {
            return true;
        }

        $val = $this->redis->get( $this->key );

        return $val !== false;
    }

    /**
    * Delete the users token. Aka, log out.
    */
    public function clearToken()
    {
        $this->cachedToken = null;
        $this->redis->delete($this->key);
    }
}
