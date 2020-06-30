<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Strava;
use OAuth\Common\Token\TokenInterface;
use PHPUnit\Framework\TestCase;

class StravaTest extends TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://www.strava.com/oauth/authorize?approval_prompt=auto',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );

        // Verify that 'offine' works
        $service->setApprouvalPrompt('force');
        $this->assertSame(
            'https://www.strava.com/oauth/authorize?approval_prompt=force',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );

    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpointException()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('OAuth\OAuth2\Service\Exception\InvalidAccessTypeException');

        try {
            $service->setApprouvalPrompt('invalid');
        } catch (InvalidAccessTypeException $e) {
            return;
        }
        $this->fail('Expected InvalidAccessTypeException not thrown');
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://www.strava.com/oauth/token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Strava::__construct
     * @covers OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
