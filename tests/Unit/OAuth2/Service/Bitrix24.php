<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Bitrix24;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class Bitrix24 extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('https://bitrix24.com/oauth/authorize/', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('https://bitrix24.com/oauth/token/', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::getAuthorizationMethod
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

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        self::assertSame('access_token=foo', $absoluteUri['query']);
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"error":"some_error"}');

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('{"access_token":"foo","expires_in":"bar"}');

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers \OAuth\OAuth2\Service\Bitrix24::getExtraOAuthHeaders
     */
    public function testGetExtraOAuthHeaders(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnCallback(function ($uri, $params, $extraHeaders) {
            Assert::assertTrue(array_key_exists('Accept', $extraHeaders));
            Assert::assertTrue(in_array('application/json', $extraHeaders, true));

            return '{"access_token":"foo","expires_in":"bar"}';
        });

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
