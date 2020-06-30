<?php

namespace OAuthTest\Unit\OAuth2\Service;

use DateTime;
use OAuth\Common\Token\TokenInterface;
use OAuthTest\Mocks\OAuth2\Service\Mock;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth2\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                [],
            ]
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     */
    public function testConstructCorrectParent(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth2\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                [],
            ]
        );

        self::assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     */
    public function testConstructCorrectParentCustomUri(): void
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth2\\Service\\AbstractService',
            [
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                [],
                $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            ]
        );

        self::assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::isValidScope
     */
    public function testConstructThrowsExceptionOnInvalidScope(): void
    {
        $this->expectException('\\OAuth\\OAuth2\\Service\\Exception\\InvalidScopeException');

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['invalidscope']
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithoutParametersOrScopes(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::once())->method('getConsumerId')->willReturn('foo');
        $credentials->expects(self::once())->method('getCallbackUrl')->willReturn('bar');

        $service = new Mock(
            $credentials,
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
            $service->getAuthorizationUri()->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithParametersWithoutScopes(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::once())->method('getConsumerId')->willReturn('foo');
        $credentials->expects(self::once())->method('getCallbackUrl')->willReturn('bar');

        $service = new Mock(
            $credentials,
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertSame(
            'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
            $service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
     * @covers \OAuth\OAuth2\Service\AbstractService::isValidScope
     */
    public function testGetAuthorizationUriWithParametersAndScopes(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::once())->method('getConsumerId')->willReturn('foo');
        $credentials->expects(self::once())->method('getCallbackUrl')->willReturn('bar');

        $service = new Mock(
            $credentials,
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['mock', 'mock2']
        );

        self::assertSame(
            'http://pieterhordijk.com/auth?foo=bar&baz=beer&type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=mock+mock2',
            $service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])->getAbsoluteUri()
        );
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraOAuthHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::requestAccessToken
     * @covers \OAuth\OAuth2\Service\AbstractService::service
     */
    public function testRequestAccessToken(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceof('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('code'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
     * @covers \OAuth\OAuth2\Service\AbstractService::request
     */
    public function testRequestThrowsExceptionWhenTokenIsExpired(): void
    {
        $tokenExpiration = new DateTime('26-03-1984 00:00:00');

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::any())->method('getEndOfLife')->willReturn($tokenExpiration->format('U'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $storage
        );

        $this->expectException('\\OAuth\\Common\\Token\\Exception\\ExpiredTokenException', 'Token expired on 03/26/1984 at 12:00:00 AM');

        $service->request('https://pieterhordijk.com/my/awesome/path');
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::request
     * @covers \OAuth\OAuth2\Service\AbstractService::service
     */
    public function testRequestOauthAuthorizationMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(2);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        self::assertArrayHasKey('Authorization', $headers);
        self::assertTrue(in_array('OAuth foo', $headers, true));
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::request
     * @covers \OAuth\OAuth2\Service\AbstractService::service
     */
    public function testRequestQueryStringMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(0);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $service->setAuthorizationMethod('querystring');

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        self::assertSame('access_token=foo', $absoluteUri['query']);
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::request
     * @covers \OAuth\OAuth2\Service\AbstractService::service
     */
    public function testRequestQueryStringTwoMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(0);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $service->setAuthorizationMethod('querystring2');

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        self::assertSame('oauth2_access_token=foo', $absoluteUri['query']);
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
     * @covers \OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::request
     * @covers \OAuth\OAuth2\Service\AbstractService::service
     */
    public function testRequestBearerMethod(): void
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects(self::once())->method('retrieveResponse')->willReturnArgument(2);

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects(self::once())->method('getEndOfLife')->willReturn(TokenInterface::EOL_NEVER_EXPIRES);
        $token->expects(self::once())->method('getAccessToken')->willReturn('foo');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects(self::once())->method('retrieveAccessToken')->willReturn($token);

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $service->setAuthorizationMethod('bearer');

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        self::assertArrayHasKey('Authorization', $headers);
        self::assertTrue(in_array('Bearer foo', $headers, true));
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getStorage
     */
    public function testGetStorage(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $service->getStorage());
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
     * @covers \OAuth\OAuth2\Service\AbstractService::getExtraOAuthHeaders
     * @covers \OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
     * @covers \OAuth\OAuth2\Service\AbstractService::refreshAccessToken
     */
    public function testRefreshAccessTokenSuccess(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token');
        $token->expects(self::once())->method('getRefreshToken')->willReturn('foo');

        self::assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->refreshAccessToken($token));
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::isValidScope
     */
    public function testIsValidScopeTrue(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertTrue($service->isValidScope('mock'));
    }

    /**
     * @covers \OAuth\OAuth2\Service\AbstractService::__construct
     * @covers \OAuth\OAuth2\Service\AbstractService::isValidScope
     */
    public function testIsValidScopeFalse(): void
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertFalse($service->isValidScope('invalid'));
    }
}
