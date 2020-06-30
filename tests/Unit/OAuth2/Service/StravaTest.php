<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Strava;
use PHPUnit\Framework\TestCase;

class StravaTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'https://www.strava.com/oauth/authorize?approval_prompt=auto',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );

        // Verify that 'offine' works
        $service->setApprouvalPrompt('force');
        self::assertSame(
            'https://www.strava.com/oauth/authorize?approval_prompt=force',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpointException(): void
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
        self::fail('Expected InvalidAccessTypeException not thrown');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'https://www.strava.com/oauth/token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('error=some_error');

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Strava::__construct
     * @covers \OAuth\OAuth2\Service\Strava::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"access_token":"foo","expires_in":"bar"}');

        $service = new Strava(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
