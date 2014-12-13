<?php

namespace OAuthTest\Unit\Common\Storage;

use Doctrine\Common\Cache\ArrayCache;
use OAuth\Common\Storage\Doctrine;

class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     *
     * @runInSeparateProcess
     */
    public function testConstructCorrectInterface()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $storage);
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsAlreadyArray()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Doctrine',
            $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     *
     * @runInSeparateProcess
     */
    public function testStoreAccessTokenIsNotArray()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $_Doctrine['lusitanian_oauth_token'] = 'foo';

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Storage\\Doctrine',
            $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'))
        );
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     * @covers OAuth\Common\Storage\Doctrine::retrieveAccessToken
     * @covers OAuth\Common\Storage\Doctrine::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenValid()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertInstanceOf('\\OAuth\\Common\\Token\\TokenInterface', $storage->retrieveAccessToken('foo'));
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::retrieveAccessToken
     * @covers OAuth\Common\Storage\Doctrine::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testRetrieveAccessTokenThrowsExceptionWhenTokenIsNotFound()
    {
        $this->setExpectedException('\\OAuth\\Common\\Storage\\Exception\\TokenNotFoundException');

        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $storage->retrieveAccessToken('foo');
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     * @covers OAuth\Common\Storage\Doctrine::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenTrue()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::hasAccessToken
     *
     * @runInSeparateProcess
     */
    public function testHasAccessTokenFalse()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $this->assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::clearToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenIsNotSet()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Doctrine', $storage->clearToken('foo'));
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     * @covers OAuth\Common\Storage\Doctrine::clearToken
     *
     * @runInSeparateProcess
     */
    public function testClearTokenSet()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Doctrine', $storage->clearToken('foo'));
        $this->assertFalse($storage->hasAccessToken('foo'));
    }

    /**
     * @covers OAuth\Common\Storage\Doctrine::__construct
     * @covers OAuth\Common\Storage\Doctrine::storeAccessToken
     * @covers OAuth\Common\Storage\Doctrine::clearAllTokens
     *
     * @runInSeparateProcess
     */
    public function testClearAllTokens()
    {
        $storage = new Doctrine(
            new ArrayCache(),
            "aa",
            "bb"
        );

        $storage->storeAccessToken('foo', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'));
        $storage->storeAccessToken('bar', $this->getMock('\\OAuth\\Common\\Token\\TokenInterface'));

        $this->assertTrue($storage->hasAccessToken('foo'));
        $this->assertTrue($storage->hasAccessToken('bar'));
        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\Doctrine', $storage->clearAllTokens());
        $this->assertFalse($storage->hasAccessToken('foo'));
        $this->assertFalse($storage->hasAccessToken('bar'));
    }
}
