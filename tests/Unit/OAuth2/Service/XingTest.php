<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Xing;
use PHPUnit\Framework\TestCase;

class XingTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     * @covers \OAuth\OAuth2\Service\Xing::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://api.xing.com/auth/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     * @covers \OAuth\OAuth2\Service\Xing::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://api.xing.com/auth/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     * @covers \OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     * @covers \OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $error = [
            'error' => 'some_error',
            'error_description' => 'something went very wrong',
            'error_uri' => 'this imaginary link contains more information',
        ];

        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(json_encode($error));

        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Xing::__construct
     * @covers \OAuth\OAuth2\Service\Xing::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}');

        $service = new Xing(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
