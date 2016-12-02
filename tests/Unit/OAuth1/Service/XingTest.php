<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Xing;

class XingTest extends AbstractTokenParserTest
{
    
    protected function getClassName()
    {
        return '\OAuth\OAuth1\Service\Xing';
    }
    
    private $client;
    private $storage;
    private $xing;


    public function setUp()
    {
        parent::setUp();

        $this->client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $this->storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');

        $this->xing = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->client,
            $this->storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\ServiceInterface', $this->xing
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService', $this->xing
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->client,
            $this->storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     * @covers OAuth\OAuth1\Service\Xing::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/request_token',
            $this->xing->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     * @covers OAuth\OAuth1\Service\Xing::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/authorize',
            $this->xing->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Xing::__construct
     * @covers OAuth\OAuth1\Service\Xing::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $this->assertSame(
            'https://api.xing.com/v1/access_token',
            $this->xing->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }
    
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
                '{"error_name":"bar"}'
        ));

        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));
        $token->expects($this->once())->method('getAccessTokenSecret')->willReturn('baz');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new $this->serviceName(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

}