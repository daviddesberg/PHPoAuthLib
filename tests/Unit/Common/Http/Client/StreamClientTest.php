<?php

namespace OAuthTest\Unit\Common\Http\Client;

use OAuth\Common\Http\Client\StreamClient;

class StreamClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testConstructCorrectInstance()
    {
        $client = new StreamClient();

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBody()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new StreamClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'GET'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     */
    public function testRetrieveResponseThrowsExceptionOnGetRequestWithBodyMethodConvertedToUpper()
    {
        $this->setExpectedException('\\InvalidArgumentException');

        $client = new StreamClient();

        $client->retrieveResponse(
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            'foo',
            array(),
            'get'
        );
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponseWithCustomContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/get'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponseWithFormUrlEncodedContentType()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('application/x-www-form-urlencoded', $response['headers']['Content-Type']);
        $this->assertEquals(['foo' => 'bar', 'baz' => 'fab'], $response['form']);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponseHost()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            ['foo' => 'bar', 'baz' => 'fab'],
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('httpbin.org', $response['headers']['Host']);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponsePostRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/post'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'POST'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponsePutRequestWithRequestBodyAsString()
    {
        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('httpbin.org'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('http://httpbin.org/put'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            'foo',
            array(),
            'PUT'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo', $response['data']);
    }

    /**
     * @covers OAuth\Common\Http\Client\StreamClient::retrieveResponse
     * @covers OAuth\Common\Http\Client\StreamClient::generateStreamContext
     */
    public function testRetrieveResponseThrowsExceptionOnInvalidRequest()
    {
        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $endPoint = $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $endPoint->expects($this->any())
            ->method('getHost')
            ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'));
        $endPoint->expects($this->any())
            ->method('getAbsoluteUri')
            ->will($this->returnValue('dskjhfckjhekrsfhkehfkreljfrekljfkre'));

        $client = new StreamClient();

        $response = $client->retrieveResponse(
            $endPoint,
            '',
            array('Content-type' => 'foo/bar'),
            'get'
        );

        $response = json_decode($response, true);

        $this->assertSame('foo/bar', $response['headers']['Content-Type']);
    }

    /**
     * Any implementing HTTP providers should send a request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param UriInterface $endpoint
     * @param mixed        $requestBody
     * @param array        $extraHeaders
     * @param string       $method
     *
     * @return string
     *
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     */
    public function retrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {
        // Normalize method name
        $method = strtoupper($method);

        $this->normalizeHeaders($extraHeaders);

        if ($method === 'GET' && !empty($requestBody)) {
            throw new \InvalidArgumentException('No body expected for "GET" request.');
        }

        if (!isset($extraHeaders['Content-type']) && $method === 'POST' && is_array($requestBody)) {
            $extraHeaders['Content-type'] = 'Content-type: application/x-www-form-urlencoded';
        }

        $extraHeaders['Host']       = 'Host: '.$endpoint->getHost();
        $extraHeaders['Connection'] = 'Connection: close';

        if (is_array($requestBody)) {
            $requestBody = http_build_query($requestBody, null, '&');
        }

        $context = $this->generateStreamContext($requestBody, $extraHeaders, $method);

        $level = error_reporting(0);
        $response = file_get_contents($endpoint->getAbsoluteUri(), false, $context);
        error_reporting($level);
        if (false === $response) {
            $lastError = error_get_last();
            if (is_null($lastError)) {
                throw new TokenResponseException('Failed to request resource.');
            }
            throw new TokenResponseException($lastError['message']);
        }

        return $response;
    }

    private function generateStreamContext($body, $headers, $method)
    {
        return stream_context_create(
            array(
                'http' => array(
                    'method'           => $method,
                    'header'           => array_values($headers),
                    'content'          => $body,
                    'protocol_version' => '1.1',
                    'user_agent'       => 'Lusitanian OAuth Client',
                    'max_redirects'    => $this->maxRedirects,
                    'timeout'          => $this->timeout
                ),
            )
        );
    }
}
