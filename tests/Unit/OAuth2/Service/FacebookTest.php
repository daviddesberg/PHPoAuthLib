<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Facebook;
use PHPUnit\Framework\TestCase;

class FacebookTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://www.facebook.com/dialog/oauth', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame('https://graph.facebook.com/oauth/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testGetAuthorizationMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(2);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        self::assertArrayHasKey('Authorization', $headers);
        self::assertTrue(in_array('OAuth foo', $headers, true));
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('error=some_error');

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('access_token=foo&expires=bar');

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('access_token=foo&expires=bar&refresh_token=baz');

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriRedirectUriMissing(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->expectException('\\OAuth\\Common\\Exception\\Exception');

        $service->getDialogUri('feed', []);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriInstanceofUri(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');

        $service = new Facebook(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $dialogUri = $service->getDialogUri(
            'feed',
            [
                'redirect_uri' => 'http://www.facebook.com',
                'state' => 'Random state',
            ]
        );
        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\Uri', $dialogUri);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Facebook::__construct
     * @covers \OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriContainsAppIdAndOtherParameters(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())->method('getConsumerId')->willReturn('application_id');

        $service = new Facebook(
            $credentials,
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $dialogUri = $service->getDialogUri(
            'feed',
            [
                'redirect_uri' => 'http://www.facebook.com',
                'state' => 'Random state',
            ]
        );

        $queryString = $dialogUri->getQuery();
        parse_str($queryString, $queryArray);

        self::assertArrayHasKey('app_id', $queryArray);
        self::assertArrayHasKey('redirect_uri', $queryArray);
        self::assertArrayHasKey('state', $queryArray);
    }
}
