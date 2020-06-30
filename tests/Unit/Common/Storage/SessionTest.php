<?php

namespace OAuthTest\Unit\Common\Storage;

use OAuth\Common\Storage\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * @covers Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructCorrectInterface()
    {
        $storage = new Session();

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructWithoutStartingSession()
    {
        session_start();

        $storage = new Session(false);

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructTryingToStartWhileSessionAlreadyExists()
    {
        session_start();

        $storage = new Session();

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers Session::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructWithExistingSessionKey()
    {
        session_start();

        $_SESSION['lusitanian_oauth_token'] = array();

        $storage = new Session();

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsAlreadyArray()
    {
        $storage = new Session();

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Session',
            $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsNotArray()
    {
        $storage = new Session();

        $_SESSION['lusitanian_oauth_token'] = 'foo';

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Session',
            $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     * @covers Session::retrieveAccessToken
     * @covers Session::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenValid()
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertInstanceOf('\\OAuth\\Common\\Token\\TokenInterface', $storage->retrieveAccessToken('foo'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::retrieveAccessToken
     * @covers Session::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound()
    {
        $this->expectException('\\OAuth\\Common\\Storage\\Exception\\TokenNotFoundException');

        $storage = new Session();

        $storage->retrieveAccessToken('foo');
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     * @covers Session::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenTrue()
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenFalse()
    {
        $storage = new Session();

        $this->assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::clearToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenIsNotSet()
    {
        $storage = new Session();

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearToken('foo'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     * @covers Session::clearToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenSet()
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearToken('foo'));
        $this->assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::storeAccessToken
     * @covers Session::clearAllTokens
     *
     * @runInSeparateProcess
     */
    public function testClearAllTokens()
    {
        $storage = new Session();

        $storage->storeAccessToken('foo', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));
        $storage->storeAccessToken('bar', $this->createMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
        $this->assertTrue($storage->hasAccessToken('bar'));
        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Session', $storage->clearAllTokens());
        $this->assertFalse($storage->hasAccessToken('foo'));
        $this->assertFalse($storage->hasAccessToken('bar'));
    }

    /**
     * @covers Session::__construct
     * @covers Session::__destruct
     *
     * @runInSeparateProcess
     */
    public function testDestruct()
    {
        $storage = new Session();

        unset($storage);
    }

    /**
     * @covers Session::storeAccessToken
     * @covers Session::retrieveAccessToken
     *
     * @runInSeparateProcess
     */
    public function testSerializeUnserialize()
    {
        $mock = $this->createMock('\\OAuth\\Common\\Token\\AbstractToken', array('__sleep'));
        $mock->expects($this->once())
            ->method('__sleep')
            ->will($this->returnValue(array('accessToken')));

        $storage = new Session();
        $storage->storeAccessToken('foo', $mock);
        $retrievedToken = $storage->retrieveAccessToken('foo');

        $this->assertInstanceOf('\\OAuth\\Common\\Token\\AbstractToken', $retrievedToken);
    }
}
