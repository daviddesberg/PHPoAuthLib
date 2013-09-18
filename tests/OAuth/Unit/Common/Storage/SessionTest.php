<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\Session;
use OAuth\Unit\Common\Storage\StorageTest;
use OAuth\OAuth2\Token\StdOAuth2Token;

class SessionTest extends StorageTest
{
    public function setUp()
    {
        // set it
        $_SESSION = array();
        $this->storage = new Session();
    }

    public function tearDown()
    {
        // empty
    }

    /**
     * Check that the token survives the constructor
     */
    public function testStorageSurvivesConstructor()
    {
        $service = 'Facebook';
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param'));

        // act
        $this->storage->storeAccessToken($service, $token);
        $this->storage = null;
        $this->storage = new Session(false);

        // assert
        $extraParams = $this->storage->retrieveAccessToken($service)->getExtraParams();
        $this->assertEquals('param', $extraParams['extra']);
        $this->assertEquals($token, $this->storage->retrieveAccessToken($service));
    }
}
