<?php

namespace OAuthTest\Unit\Common\Service;

use OAuthTest\Mocks\Common\Service\Mock;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    /**
     * @covers \AbstractService::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ]
        );

        self::assertInstanceOf('\\OAuth\\Common\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::getStorage
     */
    public function testGetStorage(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ]
        );

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $service->getStorage());
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::service
     */
    public function testService(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('Mock', $service->service());
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingUriObject(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $service->testDetermineRequestUriFromPath($this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'))
        );
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpPath(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath('http://example.com');

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpsPath(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $uri = $service->testDetermineRequestUriFromPath('https://example.com');

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        self::assertSame('https://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathThrowsExceptionOnInvalidUri(): void
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
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithQueryString(): void
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

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        self::assertSame('https://example.com/path?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \AbstractService::__construct
     * @covers \AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithLeadingSlashInPath(): void
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

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
        self::assertSame('https://example.com/path', $uri->getAbsoluteUri());
    }
}
