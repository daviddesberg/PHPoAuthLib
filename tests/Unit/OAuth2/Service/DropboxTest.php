<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Dropbox;
use PHPUnit\Framework\TestCase;

class DropboxTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithoutAdditionalParams(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::once())->method('getConsumerId')->willReturn('foo');
        $credentials->expects(self::once())->method('getCallbackUrl')->willReturn('bar');

        $service = new Dropbox(
            $credentials,
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
            $service->getAuthorizationUri()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithAdditionalParams(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::once())->method('getConsumerId')->willReturn('foo');
        $credentials->expects(self::once())->method('getCallbackUrl')->willReturn('bar');

        $service = new Dropbox(
            $credentials,
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
            $service->getAuthorizationUri()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://www.dropbox.com/1/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://api.dropbox.com/1/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(0);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        self::assertSame('access_token=foo', $absoluteUri['query']);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('error=some_error');

        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"access_token":"foo","expires_in":"bar"}');

        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\Dropbox::__construct
     * @covers \OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}');

        $service = new Dropbox(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
