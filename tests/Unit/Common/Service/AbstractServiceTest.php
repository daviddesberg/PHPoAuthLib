<?php

namespace OAuthTest\Unit\Common\Service;

use OAuthTest\Mocks\Common\Service\Mock;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    /**
     * @covers AbstractService::__construct
     */
    public function testConstructCorrectInterface()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            array(
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            )
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::getStorage
     */
    public function testGetStorage()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            array(
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            )
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $service->getStorage());
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::service
     */
    public function testService()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('Mock', $service->service());
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingUriObject()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $service->testDetermineRequestUriFromPath($this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'))
        );
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpPath()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath('http://example.com');

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        $this->assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpsPath()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath('https://example.com');

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        $this->assertSame('https://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathThrowsExceptionOnInvalidUri()
    {
        $this->expectException('\\OAuth\\Common\\Exception\\Exception');

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath('example.com');
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithQueryString()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath(
            'path?param1=value1',
            new \OAuth\Common\Http\Uri\Uri('https://example.com')
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        $this->assertSame('https://example.com/path?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers AbstractService::__construct
     * @covers AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithLeadingSlashInPath()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath(
            '/path',
            new \OAuth\Common\Http\Uri\Uri('https://example.com')
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        $this->assertSame('https://example.com/path', $uri->getAbsoluteUri());
    }
}
