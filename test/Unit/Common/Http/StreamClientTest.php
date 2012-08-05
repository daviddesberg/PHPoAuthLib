<?php
use OAuth\Common\Http\StreamClient;
use OAuth\Common\Http\Uri;

class StreamClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var object|StreamClient
     */
    protected $streamClient;

    public function setUp()
    {
        $this->streamClient = new StreamClient();
    }

    public function tearDown()
    {
        unset($this->streamClient);
    }

    public function testRetrieveResponse()
    {
        $testUri = new Uri('httpbin.org', '/post');

        // if the request fails, an exception will be thrown
        $response = $this->streamClient->retrieveResponse($testUri, ['testKey' => 'testValue'] );

        // verify hte post response
        $data = json_decode($response, true);
        $this->assertEquals( 'testValue', $data['form']['testKey'] );
    }
}