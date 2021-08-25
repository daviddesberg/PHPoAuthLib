<?php

namespace OAuthTest\Unit\Common\Storage;

use OAuth\Common\Storage\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @covers \Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructCorrectInterface(): void
    {
        $storage = new Session();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers \Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructWithoutStartingSession(): void
    {
        session_start();

        $storage = new Session(false);

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers \Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructTryingToStartWhileSessionAlreadyExists(): void
    {
        session_start();

        $storage = new Session();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers \Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructWithExistingSessionKey(): void
    {
        session_start();

        $_SESSION['lusitanian_oauth_token'] = [];

        $storage = new Session();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsAlreadyArray(): void
    {
        $storage = new Session();

        self::assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Session',
            $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsNotArray(): void
    {
        $storage = new Session();

        $_SESSION['lusitanian_oauth_token'] = 'foo';

        self::assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Session',
            $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::hasAccessToken
     * @covers \Session::retrieveAccessToken
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenValid(): void
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertInstanceOf('\\OAuth\\Common\\Token\\TokenInterface', $storage->retrieveAccessToken('foo'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::hasAccessToken
     * @covers \Session::retrieveAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(): void
    {
        $this->expectException('\\OAuth\\Common\\Storage\\Exception\\TokenNotFoundException');

        $storage = new Session();

        $storage->retrieveAccessToken('foo');
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::hasAccessToken
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenTrue(): void
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenFalse(): void
    {
        $storage = new Session();

        self::assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::clearToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenIsNotSet(): void
    {
        $storage = new Session();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearToken('foo'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::clearToken
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenSet(): void
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearToken('foo'));
        self::assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::clearAllTokens
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testClearAllTokens(): void
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));
        $storage->storeAccessToken('bar', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
        self::assertTrue($storage->hasAccessToken('bar'));
        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearAllTokens());
        self::assertFalse($storage->hasAccessToken('foo'));
        self::assertFalse($storage->hasAccessToken('bar'));
    }

    /**
     * @covers \Session::__construct
     * @covers \Session::__destruct
     *
     * @runInSeparateProcess
     */
    public function testDestruct(): void
    {
        $storage = new Session();

        unset($storage);
    }

    /**
     * @covers \Session::retrieveAccessToken
     * @covers \Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testSerializeUnserialize(): void
    {
        $mock = $this->createMock('\\OAuth\\Common\\Token\\AbstractToken', ['__sleep']);
        $mock->expects(self::once())
            ->method('__sleep')
            ->willReturn(['accessToken']);

        $storage = new Session();
        $storage->storeAccessToken('foo', $mock);
        $retrievedToken = $storage->retrieveAccessToken('foo');

        self::assertInstanceOf('\\OAuth\\Common\\Token\\AbstractToken', $retrievedToken);
    }
}
