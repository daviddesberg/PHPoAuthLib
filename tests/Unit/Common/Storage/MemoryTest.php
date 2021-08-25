<?php

namespace OAuthTest\Unit\Common\Storage;

use OAuth\Common\Storage\Memory;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $storage = new Memory();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::storeAccessToken
     */
    public function testStoreAccessToken(): void
    {
        $storage = new Memory();

        self::assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Memory',
            $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::hasAccessToken
     * @covers \OAuth\Common\Storage\Memory::retrieveAccessToken
     * @covers \OAuth\Common\Storage\Memory::storeAccessToken
     */
    public function testRetrieveAccessTokenValid(): void
    {
        $storage = new Memory();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertInstanceOf('\\OAuth\\Common\\Token\\TokenInterface', $storage->retrieveAccessToken('foo'));
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::hasAccessToken
     * @covers \OAuth\Common\Storage\Memory::retrieveAccessToken
     */
    public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound(): void
    {
        $this->expectException('\\OAuth\\Common\\Storage\\Exception\\TokenNotFoundException');

        $storage = new Memory();

        $storage->retrieveAccessToken('foo');
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::hasAccessToken
     * @covers \OAuth\Common\Storage\Memory::storeAccessToken
     */
    public function testHasAccessTokenTrue(): void
    {
        $storage = new Memory();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::hasAccessToken
     */
    public function testHasAccessTokenFalse(): void
    {
        $storage = new Memory();

        self::assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::clearToken
     */
    public function testClearTokenIsNotSet(): void
    {
        $storage = new Memory();

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Memory', $storage->clearToken('foo'));
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::clearToken
     * @covers \OAuth\Common\Storage\Memory::storeAccessToken
     */
    public function testClearTokenSet(): void
    {
        $storage = new Memory();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Memory', $storage->clearToken('foo'));
        self::assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers \OAuth\Common\Storage\Memory::__construct
     * @covers \OAuth\Common\Storage\Memory::clearAllTokens
     * @covers \OAuth\Common\Storage\Memory::storeAccessToken
     */
    public function testClearAllTokens(): void
    {
        $storage = new Memory();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));
        $storage->storeAccessToken('bar', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        self::assertTrue($storage->hasAccessToken('foo'));
        self::assertTrue($storage->hasAccessToken('bar'));
        self::assertInstanceOf('\\OAuth\\Common\\Storage\\Memory', $storage->clearAllTokens());
        self::assertFalse($storage->hasAccessToken('foo'));
        self::assertFalse($storage->hasAccessToken('bar'));
    }
}
