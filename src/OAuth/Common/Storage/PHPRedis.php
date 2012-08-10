<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Storage
 * @author     David Desberg <david@thedesbergs.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

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
        $val = $this->redis->get( $this->key );
        if( false === $val ) {
            throw new TokenNotFoundException('Token not found in redis');
        }

        return unserialize( $val );
    }

    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     * @throws StorageException
     */
    public function storeAccessToken(TokenInterface $token)
    {
        // let redis exceptions bubble up
        if( $this->redis->set($this->key, serialize($token)) ) {
            return;
        }

        throw new StorageException('Unable to store token');
    }
}
