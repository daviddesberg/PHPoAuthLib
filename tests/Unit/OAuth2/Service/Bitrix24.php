<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Bitrix24;
use OAuth\Common\Token\TokenInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class Bitrix24Test extends TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('https://bitrix24.com/oauth/authorize/', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('https://bitrix24.com/oauth/token/', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnArgument(0));

        $token = $this->createMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        $this->assertSame('access_token=foo', $absoluteUri['query']);
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"error":"some_error"}'));

        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Bitrix24::__construct
     * @covers OAuth\OAuth2\Service\Bitrix24::getExtraOAuthHeaders
     * @throws \OAuth\OAuth2\Service\Exception\InvalidScopeException
     */
    public function testGetExtraOAuthHeaders()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnCallback(function($uri, $params, $extraHeaders) {
            Assert::assertTrue(array_key_exists('Accept', $extraHeaders));
            Assert::assertTrue(in_array('application/json', $extraHeaders, true));

            return '{"access_token":"foo","expires_in":"bar"}';
        }));

        $service = new Bitrix24(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
