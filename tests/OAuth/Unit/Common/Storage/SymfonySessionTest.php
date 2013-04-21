<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth2\Token\StdOAuth2Token;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SymfonySessionTest extends PHPUnit_Framework_TestCase
{
    public function testStoresInMemory()
    {
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param'));

        $session = new Session(new MockArraySessionStorage());
        $storage = new SymfonySession($session);
        $storage->storeAccessToken($token);

        $extraParams = $storage->retrieveAccessToken()->getExtraParams();
        $this->assertEquals('param', $extraParams['extra']);
        $this->assertEquals('access', $storage->retrieveAccessToken()->getAccessToken());
    }

    /**
     * @test
     * @expectedException OAuth\Common\Storage\Exception\TokenNotFoundException
     */
    public function retrievingNonExistentTokenShouldThrowException()
    {
        $session = new Session(new MockArraySessionStorage());
        $storage = new SymfonySession($session);

        $nonExistentToken = $storage->retrieveAccessToken();
    }

    public function testStorageClears()
    {
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param'));

        $session = new Session(new MockArraySessionStorage());
        $storage = new SymfonySession($session);
        $storage->storeAccessToken($token);
        $this->assertNotNull($storage->retrieveAccessToken());

        $storage->clearToken();

        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $storage->retrieveAccessToken();
    }
}
