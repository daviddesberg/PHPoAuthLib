<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Twitter;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class TwitterTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertSame(
            'https://api.twitter.com/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertTrue(
            in_array(
                strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()),
                [\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE]
            )
        );

        $service->setAuthorizationEndpoint(\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE);

        self::assertTrue(
            in_array(
                strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()),
                [\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE]
            )
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::setAuthorizationEndpoint
     */
    public function testSetAuthorizationEndpoint(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Exception\\Exception');

        $service->setAuthorizationEndpoint('foo');
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint(): void
    {
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertSame(
            'https://api.twitter.com/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(null);

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('notanarray');

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('foo=bar');

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(
            'oauth_callback_confirmed=false'
        );

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     * @covers \OAuth\OAuth1\Service\Twitter::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseValid(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        );

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('error=bar');

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::__construct
     * @covers \OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
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

        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenErrorTotalBullshit(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
        $method = new ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
        $method->setAccessible(true);
        $method->invokeArgs($service, ['hoho']);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenErrorItsAnError(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
        $method = new ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
        $method->setAccessible(true);
        $method->invokeArgs($service, ['error=hihihaha']);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenErrorItsMissingOauthToken(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
        $method = new ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
        $method->setAccessible(true);
        $method->invokeArgs($service, ['oauth_token_secret=1']);
    }

    /**
     * @covers \OAuth\OAuth1\Service\Twitter::parseAccessTokenResponse
     */
    public function testParseAccessTokenErrorItsMissingOauthTokenSecret(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $service = new Twitter(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
        $method = new ReflectionMethod(get_class($service), 'parseAccessTokenResponse');
        $method->setAccessible(true);
        $method->invokeArgs($service, ['oauth_token=1']);
    }
}
