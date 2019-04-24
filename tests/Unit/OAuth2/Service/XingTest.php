<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Xing;

class XingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     * @covers OAuth\OAuth2\Service\Xing::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://api.xing.com/auth/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     * @covers OAuth\OAuth2\Service\Xing::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://api.xing.com/auth/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     * @covers OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     * @covers OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $error = array(
            'error' => 'some_error',
            'error_description' => 'something went very wrong',
            'error_uri' => 'this imaginary link contains more information'
        );

        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(json_encode($error)));

        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Xing::__construct
     * @covers OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

        $service = new Xing(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
