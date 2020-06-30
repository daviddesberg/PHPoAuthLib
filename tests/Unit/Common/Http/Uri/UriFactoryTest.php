<?php

namespace OAuthTest\Unit\Common\Http\Uri;

use OAuth\Common\Http\Uri\UriFactory;
use PHPUnit\Framework\TestCase;

class UriFactoryTest extends TestCase
{
    public function testConstructCorrectInterface(): void
    {
        $factory = new UriFactory();

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriFactoryInterface', $factory);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     */
    public function testCreateFromSuperGlobalArrayUsingProxyStyle(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray(['REQUEST_URI' => 'http://example.com']);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayHttp(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTPS' => 'off',
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * This looks wonky David. Should the port really fallback to 80 even when supplying https as scheme?
     *
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayHttps(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTPS' => 'on',
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('https://example.com:80/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayPortSupplied(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'SERVER_PORT' => 21,
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com:21/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayPortNotSet(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayRequestUriSet(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayRedirectUrlSet(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'REDIRECT_URL' => '/foo',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayThrowsExceptionOnDetectingPathMissingIndices(): void
    {
        $factory = new UriFactory();

        $this->expectException('\\RuntimeException');

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'QUERY_STRING' => 'param1=value1',
        ]);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayWithQueryString(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo?param1=value1',
            'QUERY_STRING' => 'param1=value1',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayWithoutQueryString(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/foo',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::attemptProxyStyleParse
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromParts
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromSuperGlobalArray
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectHost
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPath
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectPort
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectQuery
     * @covers \OAuth\Common\Http\Uri\UriFactory::detectScheme
     */
    public function testCreateFromSuperGlobalArrayHostWithColon(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromSuperGlobalArray([
            'HTTP_HOST' => 'example.com:80',
            'REQUEST_URI' => '/foo',
        ]);

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com/foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\UriFactory::createFromAbsolute
     */
    public function testCreateFromAbsolute(): void
    {
        $factory = new UriFactory();

        $uri = $factory->createFromAbsolute('http://example.com');

        self::assertInstanceOf(
            '\\OAuth\\Common\\Http\\Uri\\UriInterface',
            $uri
        );

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }
}
