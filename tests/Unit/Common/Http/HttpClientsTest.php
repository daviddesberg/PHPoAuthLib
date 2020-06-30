<?php

/**
 * @author     David Desberg <david@daviddesberg.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Http;

use Closure;
use OAuth\Common\Http\Client;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use PHPUnit\Framework\TestCase;

class HttpClientsTest extends TestCase
{
    /**
     * @var ClientInterface[]|object
     */
    protected $clients;

    protected function setUp(): void
    {
        $streamClient = new Client\StreamClient();
        $streamClient->setTimeout(3);

        $curlClient = new Client\CurlClient();
        $curlClient->setTimeout(3);

        $this->clients[] = $streamClient;
        $this->clients[] = $curlClient;
    }

    protected function tearDown(): void
    {
        foreach ($this->clients as $client) {
            unset($client);
        }
    }

    /**
     * Test that extra headers are passed properly.
     */
    public function testHeaders(): void
    {
        $testUri = new Uri('http://httpbin.org/get');

        $me = $this;
        $headerCb = function ($response) use ($me): void {
            $data = json_decode($response, true);
            $me->assertEquals('extraheadertest', $data['headers']['Testingheader']);
        };

        $this->__doTestRetrieveResponse($testUri, [], ['Testingheader' => 'extraheadertest'], 'GET', $headerCb);
    }

    /**
     * Tests that we get an exception for a >= 400 status code.
     */
    public function testException(): void
    {
        // sending a post here should get us a 405 which should trigger an exception
        $testUri = new Uri('http://httpbin.org/delete');
        foreach ($this->clients as $client) {
            $this->expectException('OAuth\Common\Http\Exception\TokenResponseException');
            $client->retrieveResponse($testUri, ['blah' => 'blih']);
        }
    }

    /**
     * Tests the DELETE method.
     */
    public function testDelete(): void
    {
        $testUri = new Uri('http://httpbin.org/delete');

        $me = $this;
        $deleteTestCb = function ($response) use ($me): void {
            $data = json_decode($response, true);
            $me->assertEquals('', $data['data']);
        };

        $this->__doTestRetrieveResponse($testUri, [], [], 'DELETE', $deleteTestCb);
    }

    /**
     * Tests the PUT method.
     */
    public function testPut(): void
    {
        $testUri = new Uri('http://httpbin.org/put');

        $me = $this;
        $putTestCb = function ($response) use ($me): void {
            // verify the put response
            $data = json_decode($response, true);
            $me->assertEquals(json_encode(['testKey' => 'testValue']), $data['data']);
        };

        $this->__doTestRetrieveResponse($testUri, json_encode(['testKey' => 'testValue']), ['Content-Type' => 'application/json'], 'PUT', $putTestCb);
    }

    /**
     * Tests the POST method.
     */
    public function testPost(): void
    {
        // http test server
        $testUri = new Uri('http://httpbin.org/post');

        $me = $this;
        $postTestCb = function ($response) use ($me): void {
            // verify the post response
            $data = json_decode($response, true);
            // note that we check this because the retrieveResponse wrapper function automatically adds a content-type
            // if there isn't one and it
            $me->assertEquals('testValue', $data['form']['testKey']);
        };

        $this->__doTestRetrieveResponse($testUri, ['testKey' => 'testValue'], [], 'POST', $postTestCb);
    }

    /**
     * Expect exception when we try to send a GET request with a body.
     */
    public function testInvalidGet(): void
    {
        $testUri = new Uri('http://site.net');

        foreach ($this->clients as $client) {
            $this->expectException('InvalidArgumentException');
            $client->retrieveResponse($testUri, ['blah' => 'blih'], [], 'GET');
        }
    }

    /**
     * Tests the GET method.
     */
    public function testGet(): void
    {
        // test uri
        $testUri = new Uri('http://httpbin.org/get?testKey=testValue');

        $me = $this;
        $getTestCb = function ($response) use ($me): void {
            $data = json_decode($response, true);
            $me->assertEquals('testValue', $data['args']['testKey']);
        };

        $this->__doTestRetrieveResponse($testUri, [], [], 'GET', $getTestCb);
    }

    /**
     * Test on all HTTP clients.
     *
     * @param array        $param
     * @param string       $method
     * @param Closure     $responseCallback
     */
    protected function __doTestRetrieveResponse(UriInterface $uri, $param, array $header, $method, $responseCallback): void
    {
        foreach ($this->clients as $client) {
            $response = $client->retrieveResponse($uri, $param, $header, $method);
            $responseCallback($response, $client);
        }
    }
}
