<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\CurlClient;
use OAuth\Common\Http\Exception\TokenResponseException;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{
    /**
     *
     */
    public function testConstructCorrectInstance()
    {
        $client = new CurlClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers CurlClient::setForceSSL3
     */
    public function testSetForceSSL3()
    {
        $client = new CurlClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\CurlClient', $client->setForceSSL3(true));
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody()
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'GET'
        );
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper()
    {
        $this->expectException('\\InvalidArgumentException');

        $client = new CurlClient();

        $client->retrieveResponse(
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'get'
        );
    }

    /**
     * @covers StreamClient::retrieveResponse
     * @covers StreamClient::generateStreamContext
     */
    public function testRetrieveResponseDefaultUserAgent()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array(),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('PHPoAuthLib', $response['headers']['User-Agent']);
    }

    /**
     * @covers StreamClient::retrieveResponse
     * @covers StreamClient::generateStreamContext
     */
    public function testRetrieveResponseCustomUserAgent()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new CurlClient('My Super Awesome Http Client');

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array(),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('My Super Awesome Http Client', $response['headers']['User-Agent']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithCustomContentType()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-Type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithFormUrlEncodedContentType()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            array('foo' => 'bar', 'baz' => 'fab'),
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('application/x-www-form-urlencoded', $response['headers']['Content-Type']);
        $this->assertEquals(array('foo' => 'bar', 'baz' => 'fab'), $response['form']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseHost()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            array('foo' => 'bar', 'baz' => 'fab'),
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('httpbin.org', $response['headers']['Host']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePostRequestWithRequestBodyAsString()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $formData = array('baz' => 'fab', 'foo' => 'bar');

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            $formData,
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame($formData, $response['form']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsString()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $formData = array('baz' => 'fab', 'foo' => 'bar');

        $client = new CurlClient();

        $response = $client->retrieveResponse(
            $endPoint,
            $formData,
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame($formData, $response['form']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsStringNoRedirects()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $formData = array('baz' => 'fab', 'foo' => 'bar');

        $client = new CurlClient();

        $client->setMaxRedirects(0);

        $response = $client->retrieveResponse(
            $endPoint,
            $formData,
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame($formData, $response['form']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseWithForcedSsl3()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('https://httpbin.org/get'));

        $client = new CurlClient();

        $client->setForceSSL3(true);

        try {
            $response = $client->retrieveResponse(
                $endPoint,
                '',
                array('Content-Type' => 'foo/bar'),
                'get'
            );
        }
        catch (TokenResponseException $e) {
            if (strpos($e->getMessage(), 'cURL Error # 35') !== false) {
                $this->markTestSkipped('SSL peer handshake failed: ' . $e->getMessage());
            }
        }

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers CurlClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnInvalidUrl()
    {
        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('jkehfkefcmekjhcnkerjh'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('jkehfkefcmekjhcnkerjh'));

        $client = new CurlClient();

        $client->setForceSSL3(true);

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-Type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    public function testAdditionalParameters()
    {
        $endPoint = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/gzip'));

        $client = new CurlClient();
        $client->setCurlParameters(array(
            CURLOPT_ENCODING => 'gzip',
        ));

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array(),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertNotNull($response);
        $this->assertSame('gzip', $response['headers']['Accept-Encoding']);
        $this->assertTrue($response['gzipped']);
    }
}
