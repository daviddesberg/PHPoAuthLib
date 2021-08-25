<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\FiveHundredPx;
use PHPUnit\Framework\TestCase;

class FiveHundredPx extends TestCase
{
    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertSame(
            'https://api.500px.com/v1/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertSame(
            'https://api.500px.com/v1/oauth/authorize',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertSame(
            'https://api.500px.com/v1/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('notanarray');

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('foo=bar');

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(
            'oauth_callback_confirmed=false'
        );

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseAccessTokenResponse
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseValid(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        );

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('error=bar');

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::__construct
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\FiveHundredPx::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(
            'oauth_token=foo&oauth_token_secret=bar'
        );

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new self(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }
}
