<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Xing;
use PHPUnit\Framework\TestCase;

class XingTest extends TestCase
{
    private $client;
    private $storage;
    private $xing;


    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $this->storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');

        $this->xing = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->client,
            $this->storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );
    }

    /**
     * @covers Xing::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\ServiceInterface', $this->xing
        );
    }

    /**
     * @covers Xing::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService', $this->xing
        );
    }

    /**
     * @covers Xing::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->client,
            $this->storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/request_token',
            $this->xing->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/authorize',
            $this->xing->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/access_token',
            $this->xing->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue(null));

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $this->xing->requestRequestToken();
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue('notanarray'));

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $this->xing->requestRequestToken();
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue('foo=bar'));

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $this->xing->requestRequestToken();
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue('oauth_callback_confirmed=false'));

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $this->xing->requestRequestToken();
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseRequestTokenResponse
     * @covers Xing::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue(
                'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
            ));

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $this->xing->requestRequestToken()
        );
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue('{"message":"Invalid OAuth signature","error_name":"INVALID_OAUTH_SIGNATURE"}'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $this->storage
            ->expects($this->any())
            ->method('retrieveAccessToken')
            ->will($this->returnValue($token));

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $this->xing->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers Xing::__construct
     * @covers Xing::getRequestTokenEndpoint
     * @covers Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $this->client
            ->expects($this->once())
            ->method('retrieveResponse')
            ->will($this->returnValue('oauth_token=foo&oauth_token_secret=bar'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $this->storage
            ->expects($this->any())
            ->method('retrieveAccessToken')
            ->will($this->returnValue($token));


        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $this->xing->requestAccessToken('foo', 'bar', $token)
        );
    }
}
