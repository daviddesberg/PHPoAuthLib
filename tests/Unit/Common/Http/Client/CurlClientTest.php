<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Exception\TokenResponseException;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{
    public function testConstructCorrectInstance(): void
    {
        $client = new CurlClient();

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers \CurlClient::setForceSSL3
     */
    public function testSetForceSSL3(): void
    {
        $client = new CurlClient();

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\CurlClient', $client->setForceSSL3(true));
    }

    /**
     * @covers \CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody(): void
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            [],
            'GET'
        );
    }

    /**
     * @covers \CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper(): void
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            [],
            'get'
        );
    }

    /**
     * @covers \StreamClient::generateStreamContext
     * @covers \StreamClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \StreamClient::generateStreamContext
     * @covers \StreamClient::retrieveResponse
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

        $client = new CurlClient('My Super Awesome Http Client');

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
     * @covers \CurlClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \CurlClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \CurlClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \CurlClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \CurlClient::retrieveResponse
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

        $client = new CurlClient();

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
     * @covers \CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsStringNoRedirects(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/put');

        $formData = ['baz' => 'fab', 'foo' => 'bar'];

        $client = new CurlClient();

        $client->setMaxRedirects(0);

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
     * @covers \CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithForcedSsl3(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('https://httpbin.org/get');

        $client = new CurlClient();

        $client->setForceSSL3(true);

        try {
            $response = $client->retrieveResponse(
                $endPoint,
                '',
                ['Content-Type' => 'foo/bar'],
                'get'
            );
        } catch (TokenResponseException $e) {
            if (strpos($e->getMessage(), 'cURL Error # 35') !== false) {
                self::markTestSkipped('SSL peer handshake failed: ' . $e->getMessage());
            }
        }

        $response = json_decode($response, true);

        self::assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers \CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnInvalidUrl(): void
    {
        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('jkehfkefcmekjhcnkerjh');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('jkehfkefcmekjhcnkerjh');

        $client = new CurlClient();

        $client->setForceSSL3(true);

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            ['Content-Type' => 'foo/bar'],
            'get'
        );

        $response = json_decode($response, true);

        self::assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    public function testAdditionalParameters(): void
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects(self::any())
            ->method('getHost')
            ->willReturn('httpbin.org');
        $endPoint->expects(self::any())
            ->method('getAbsoluteUri')
            ->willReturn('http://httpbin.org/gzip');

        $client = new CurlClient();
        $client->setCurlParameters([
            CURLOPT_ENCODING => 'gzip',
        ]);

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            [],
            'get'
        );

        $response = json_decode($response, true);

        self::assertNotNull($response);
        self::assertSame('gzip', $response['headers']['Accept-Encoding']);
        self::assertTrue($response['gzipped']);
    }
}
