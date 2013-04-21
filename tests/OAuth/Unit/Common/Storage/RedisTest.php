<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Storage\PHPRedis;
use OAuth\OAuth2\Token\StdOAuth2Token;

class RedisTest extends PHPUnit_Framework_TestCase
{
    const REDIS_HOST = '127.0.0.1';
    const REDIS_PORT = '6379';

    /**
     * Check that the token gets properly stored.
     */
    public function testStorage()
    {
        if( !class_exists('\\Redis') ) {
            return; // ignore this test
        }

        // connect to a redis daemon
        $redis = new \Redis();
        $redis->connect(static::REDIS_HOST, static::REDIS_PORT);

        // create sample token
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        $redisStorage = new PHPRedis($redis, 'test_user_token');
        $redisStorage->storeAccessToken( $token );

        $extraParams = $redisStorage->retrieveAccessToken()->getExtraParams();
        $this->assertEquals( 'param', $extraParams['extra'] );
        $this->assertEquals( 'access', $redisStorage->retrieveAccessToken()->getAccessToken() );
        unset($redisStorage);
    }

    /**
     * Check that the token gets properly deleted.
     */
    public function testStorageClears()
    {
        if( !class_exists('\\Redis') ) {
            return; // ignore this test
        }

        // connect to a redis daemon
        $redis = new \Redis();
        $redis->connect(static::REDIS_HOST, static::REDIS_PORT);

        // create sample token
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        $redisStorage = new PHPRedis($redis, 'test_user_token');
        $redisStorage->storeAccessToken( $token );
        $this->assertNotNull($redisStorage->retrieveAccessToken());

        $redisStorage->clearToken();

        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $redisStorage->retrieveAccessToken();
        unset($redisStorage);
    }
}
