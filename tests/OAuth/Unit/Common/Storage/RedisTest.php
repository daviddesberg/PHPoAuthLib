<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\Redis;
use OAuth\Unit\Common\Storage\StorageTest;
use Predis\Client as Predis;

class RedisTest extends StorageTest
{
    const REDIS_HOST = '127.0.0.1';
    const REDIS_PORT = 6379;

    public function setUp()
    {
        // connect to a redis daemon
        $predis = new Predis(array(
            'host' => RedisTest::REDIS_HOST,
            'port' => RedisTest::REDIS_PORT,
        ));

        // set it
        $this->storage = new Redis($predis, 'test_user_token');
    }

    public function tearDown()
    {
        // delete
        $this->storage->clearAllTokens();

        // close connection
        $this->storage->getRedis()->quit();
    }
}
