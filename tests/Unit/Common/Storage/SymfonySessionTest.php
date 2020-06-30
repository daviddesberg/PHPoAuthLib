<?php

/**
 * @author     David Desberg <david@daviddesberg.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\SymfonySession;
use OAuth\OAuth2\Token\StdOAuth2Token;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SymfonySessionTest extends TestCase
{
    protected $session;

    protected $storage;

    protected function setUp(): void
    {
        // set it
        $this->session = new Session(new MockArraySessionStorage());
        $this->storage = new SymfonySession($this->session);
    }

    protected function tearDown(): void
    {
        // delete
        $this->storage->getSession()->clear();
        $this->storage = null;
    }

    /**
     * Check that the token survives the constructor.
     */
    public function testStorageSurvivesConstructor(): void
    {
        $service = 'Facebook';
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);

        // act
        $this->storage->storeAccessToken($service, $token);
        $this->storage = null;
        $this->storage = new SymfonySession($this->session);

        // assert
        $extraParams = $this->storage->retrieveAccessToken($service)->getExtraParams();
        self::assertEquals('param', $extraParams['extra']);
        self::assertEquals($token, $this->storage->retrieveAccessToken($service));
    }

    /**
     * Check that the token gets properly stored.
     */
    public function testStorage(): void
    {
        // arrange
        $service_1 = 'Facebook';
        $service_2 = 'Foursquare';

        $token_1 = new StdOAuth2Token('access_1', 'refresh_1', StdOAuth2Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);
        $token_2 = new StdOAuth2Token('access_2', 'refresh_2', StdOAuth2Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);

        // act
        $this->storage->storeAccessToken($service_1, $token_1);
        $this->storage->storeAccessToken($service_2, $token_2);

        // assert
        $extraParams = $this->storage->retrieveAccessToken($service_1)->getExtraParams();
        self::assertEquals('param', $extraParams['extra']);
        self::assertEquals($token_1, $this->storage->retrieveAccessToken($service_1));
        self::assertEquals($token_2, $this->storage->retrieveAccessToken($service_2));
    }

    /**
     * Test hasAccessToken.
     */
    public function testHasAccessToken(): void
    {
        // arrange
        $service = 'Facebook';
        $this->storage->clearToken($service);

        // act
        // assert
        self::assertFalse($this->storage->hasAccessToken($service));
    }

    /**
     * Check that the token gets properly deleted.
     */
    public function testStorageClears(): void
    {
        // arrange
        $service = 'Facebook';
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, ['extra' => 'param']);

        // act
        $this->storage->storeAccessToken($service, $token);
        $this->storage->clearToken($service);

        // assert
        $this->expectException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $this->storage->retrieveAccessToken($service);
    }
}
