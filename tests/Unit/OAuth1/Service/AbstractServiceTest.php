<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuthTest\Mocks\OAuth1\Service\Mock;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    /**
     * @covers \AbstractService::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            ]
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \AbstractService::__construct
     */
    public function testConstructCorrectParent(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            ]
        );

        self::assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
    }

    /**
     * @covers \AbstractService::buildAuthorizationHeaderForTokenRequest
     * @covers \AbstractService::generateNonce
     * @covers \AbstractService::getBasicAuthorizationHeaderInfo
     * @covers \AbstractService::getExtraOAuthHeaders
     * @covers \AbstractService::getSignatureMethod
     * @covers \AbstractService::getVersion
     * @covers \AbstractService::parseRequestTokenResponse
     * @covers \AbstractService::requestRequestToken
     */
    public function testRequestRequestTokenBuildAuthHeaderTokenRequestWithoutParams(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnCallback(function ($endpoint, $array, $headers): void {
            Assert::assertSame('http://pieterhordijk.com/token', $endpoint->getAbsoluteUri());
        });

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers \AbstractService::getAuthorizationEndpoint
     * @covers \AbstractService::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithoutParameters(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('http://pieterhordijk.com/auth', $service->getAuthorizationUri()->getAbsoluteUri());
    }

    /**
     * @covers \AbstractService::getAuthorizationEndpoint
     * @covers \AbstractService::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithParameters(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('http://pieterhordijk.com/auth?foo=bar&baz=beer', $service->getAuthorizationUri([
            'foo' => 'bar',
            'baz' => 'beer',
        ])->getAbsoluteUri());
    }

    /**
     * @covers \AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers \AbstractService::generateNonce
     * @covers \AbstractService::getAccessTokenEndpoint
     * @covers \AbstractService::getBasicAuthorizationHeaderInfo
     * @covers \AbstractService::getExtraOAuthHeaders
     * @covers \AbstractService::getSignatureMethod
     * @covers \AbstractService::getVersion
     * @covers \AbstractService::parseAccessTokenResponse
     * @covers \AbstractService::requestAccessToken
     * @covers \AbstractService::service
     */
    public function testRequestAccessTokenWithoutSecret(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnCallback(function ($endpoint, $array, $headers): void {
            Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
        });

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $token->expects(self::once())->method('getRequestTokenSecret')->willReturn('baz');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar'));
    }

    /**
     * @covers \AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers \AbstractService::generateNonce
     * @covers \AbstractService::getAccessTokenEndpoint
     * @covers \AbstractService::getBasicAuthorizationHeaderInfo
     * @covers \AbstractService::getExtraOAuthHeaders
     * @covers \AbstractService::getSignatureMethod
     * @covers \AbstractService::getVersion
     * @covers \AbstractService::parseAccessTokenResponse
     * @covers \AbstractService::requestAccessToken
     * @covers \AbstractService::service
     */
    public function testRequestAccessTokenWithSecret(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnCallback(function ($endpoint, $array, $headers): void {
            Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
        });

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }

    /**
     * @covers \AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers \AbstractService::determineRequestUriFromPath
     * @covers \AbstractService::generateNonce
     * @covers \AbstractService::getBasicAuthorizationHeaderInfo
     * @covers \AbstractService::getExtraApiHeaders
     * @covers \AbstractService::getSignatureMethod
     * @covers \AbstractService::getVersion
     * @covers \AbstractService::request
     * @covers \AbstractService::service
     */
    public function testRequest(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('response!');

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        //$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('response!', $service->request('/my/awesome/path'));
    }

    /**
     * This test only captures a regression in php 5.3.
     *
     * @covers \AbstractService::request
     */
    public function testRequestNonArrayBody(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturn('response!');

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::any())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertSame('response!', $service->request('/my/awesome/path', 'GET', 'A text body'));
    }
}
