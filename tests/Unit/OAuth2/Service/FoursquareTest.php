<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Foursquare;
use OAuth\Common\Token\TokenInterface;
use PHPUnit\Framework\TestCase;

class FoursquareTest extends TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://foursquare.com/oauth2/authenticate', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://foursquare.com/oauth2/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(2));

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        $this->assertTrue(array_key_exists('Authorization', $headers));
        $this->assertTrue(in_array('OAuth foo', $headers, true));
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"error":"some_error"}'));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::request
     */
    public function testRequest()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $this->assertSame(
            'https://pieterhordijk.com/my/awesome/path?v=20130829',
            $service->request('https://pieterhordijk.com/my/awesome/path')->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::request
     */
    public function testRequestShortPath()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Foursquare(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $this->assertSame(
            'https://api.foursquare.com/v2/my/awesome/path?v=20130829',
            $service->request('my/awesome/path')->getAbsoluteUri()
        );
    }
}
