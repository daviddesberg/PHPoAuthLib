<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Client;

class HttpClientsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var object|\OAuth\Common\Http\Client\ClientInterface[]
     */
    protected $clients;

    public function setUp()
    {
        $this->clients[] = new Client\StreamClient(5, 3);
    }

    public function tearDown()
    {
        foreach($this->clients as $client)
        {
            unset($client);
        }
    }

    /**
     * Test that extra headers are passed properly
     */
    public function testHeaders()
    {
        $testUri = new Uri('http://httpbin.org/get');
        $headerCb = function($response)
        {
            $data = json_decode($response, true);
            $this->assertEquals('extraheadertest', $data['headers']['Testingheader']);
        };

        $this->__doTestRetrieveResponse($testUri, [], [ 'Testingheader' => 'extraheadertest'], 'GET', $headerCb);
    }
    /**
     * Tests that we get an exception for a >= 400 status code
     */
    public function testException()
    {
        // sending a post here should get us a 405 which should trigger an exception
        $testUri = new Uri('http://httpbin.org/delete');
        foreach($this->clients as $client)
        {
            $this->setExpectedException('OAuth\Common\Http\Exception\TokenResponseException');
            $client->retrieveResponse($testUri, ['blah' => 'blih'] );
        }

    }

    /**
     * Tests the DELETE method
     */
    public function testDelete()
    {
        $testUri = new Uri('http://httpbin.org/delete');

        $deleteTestCb = function($response)
        {
            $data = json_decode($response, true);
            $this->assertEquals( '', $data['data'] );
        };

        $this->__doTestRetrieveResponse($testUri, [], [], 'DELETE', $deleteTestCb );
    }

    /**
     * Tests the PUT method
     */
    public function testPut()
    {
        $testUri = new Uri('http://httpbin.org/put');

        $putTestCb = function($response)
        {
            // verify the put response
            $data = json_decode($response, true);
            $this->assertEquals( json_encode( ['testKey' => 'testValue' ] ), $data['data'] );
        };

        $this->__doTestRetrieveResponse($testUri, json_encode( ['testKey' => 'testValue' ] ), [ 'Content-type' => 'application/json' ], 'PUT', $putTestCb );
    }

    /**
     * Tests the POST method
     */
    public function testPost()
    {
        // http test server
        $testUri = new Uri('http://httpbin.org/post');

        $postTestCb = function($response)
        {
            // verify the post response
            $data = json_decode($response, true);
            // note that we check this because the retrieveResponse wrapper function automatically adds a content-type
            // if there isn't one and it
            $this->assertEquals( 'testValue', $data['form']['testKey'] );
        };

        $this->__doTestRetrieveResponse($testUri, ['testKey' => 'testValue'], [], 'POST', $postTestCb );
    }

    /**
     * Tests the GET method
     */
    public function testGet()
    {
        // test uri
        $testUri = new Uri('http://httpbin.org/get?testKey=testValue');

        $getTestCb = function($response)
        {
            $data = json_decode($response, true);
            $this->assertEquals( 'testValue', $data['args']['testKey'] );
        };

        $this->__doTestRetrieveResponse($testUri, [], [], 'GET', $getTestCb);

    }

    /**
     * Test on all HTTP clients.
     *
     * @param OAuth\Common\Http\Uri\UriInterface $uri
     * @param array $param
     * @param array $header
     * @param $method
     * @param $responseCallback
     */
    protected function __doTestRetrieveResponse(\OAuth\Common\Http\Uri\UriInterface $uri, $param, array $header, $method, callable $responseCallback)
    {
        foreach($this->clients as $client)
        {
            $response = $client->retrieveResponse($uri, $param, $header, $method);
            $responseCallback($response, $client);
        }
    }
}