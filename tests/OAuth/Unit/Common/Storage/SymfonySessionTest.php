<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\SymfonySession;
use OAuth\Unit\Common\Storage\StorageTest;
use OAuth\OAuth2\Token\StdOAuth2Token;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SymfonySessionTest extends StorageTest
{
    protected $session;

    public function setUp()
    {
        // set it
        $this->session = new Session(new MockArraySessionStorage());
        $this->storage = new SymfonySession($this->session);
    }

    public function tearDown()
    {
        // delete
        $this->storage->getSession()->clear();
        unset($this->storage);
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
        $this->storage = new SymfonySession($this->session);

        // assert
        $extraParams = $this->storage->retrieveAccessToken($service)->getExtraParams();
        $this->assertEquals('param', $extraParams['extra']);
        $this->assertEquals($token, $this->storage->retrieveAccessToken($service));
    }
}
