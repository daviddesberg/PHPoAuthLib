<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\StreamClient;
use PHPUnit\Framework\TestCase;

class StreamClientTest extends TestCase
{
    public function testConstructCorrectInstance(): void
    {
        $client = new StreamClient();

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody(): void
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new StreamClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            [],
            'GET'
        );
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper(): void
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new StreamClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            [],
            'get'
        );
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseDefaultUserAgent(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/get');

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            [],
            'get'
        );

        $response = json_decode($response, true);

        self::assertSame('PHPoAuthLib', $response['headers']['User-Agent']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseCustomUserAgent(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/get');

        $client = new StreamClient('My Super Awesome Http Client');

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            [],
            'get'
        );

        $response = json_decode($response, true);

        self::assertSame('My Super Awesome Http Client', $response['headers']['User-Agent']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseWithCustomContentType(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/get');

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            ['Content-Type' => 'foo/bar'],
            'get'
        );

        $response = json_decode($response, true);

        self::assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseWithFormUrlEncodedContentType(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/post');

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            [],
            'POST'
        );

        $response = json_decode($response, true);

        self::assertSame('application/x-www-form-urlencoded', $response['headers']['Content-Type']);
        self::assertEquals(['foo' => 'bar', 'baz' => 'fab'], $response['form']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseHost(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/post');

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            [],
            'POST'
        );

        $response = json_decode($response, true);

        self::assertSame('httpbin.org', $response['headers']['Host']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponsePostRequestWithRequestBodyAsString(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/post');

        $formData = ['baz' => 'fab', 'foo' => 'bar'];

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            $formData,
            [],
            'POST'
        );

        $response = json_decode($response, true);

        self::assertSame($formData, $response['form']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsString(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/put');

        $formData = ['baz' => 'fab', 'foo' => 'bar'];

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            $formData,
            [],
            'PUT'
        );

        $response = json_decode($response, true);

        self::assertSame($formData, $response['form']);
    }

    /**
     * @covers \OAuth\Common\Http\Client\StreamClient::generateStreamContext
     * @covers \OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnInvalidRequest(): void
    {
        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('dskjhfckjhekrsfhkehfkreljfrekljfkre');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('dskjhfckjhekrsfhkehfkreljfrekljfkre');

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            ['Content-Type' => 'foo/bar'],
            'get'
        );

        $response = json_decode($response, true);

        self::assertSame('foo/bar', $response['headers']['Content-Type']);
    }
}
