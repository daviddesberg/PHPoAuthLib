<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuthTest\Mocks\OAuth1\Service\Mock;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AbstractServiceTest extends TestCase
{
    /**
     * @covers AbstractService::__construct
     */
    public function testConstructCorrectInterface()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            array(
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            )
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers AbstractService::__construct
     */
    public function testConstructCorrectParent()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            array(
                $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            )
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
    }

    /**
     * @covers AbstractService::requestRequestToken
     * @covers AbstractService::buildAuthorizationHeaderForTokenRequest
     * @covers AbstractService::getBasicAuthorizationHeaderInfo
     * @covers AbstractService::generateNonce
     * @covers AbstractService::getSignatureMethod
     * @covers AbstractService::getVersion
     * @covers AbstractService::getExtraOAuthHeaders
     * @covers AbstractService::parseRequestTokenResponse
     */
    public function testRequestRequestTokenBuildAuthHeaderTokenRequestWithoutParams()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnCallback(function($endpoint, $array, $headers) {
            Assert::assertSame('http://pieterhordijk.com/token', $endpoint->getAbsoluteUri());
        }));

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers AbstractService::getAuthorizationUri
     * @covers AbstractService::getAuthorizationEndpoint
     */
    public function testGetAuthorizationUriWithoutParameters()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('http://pieterhordijk.com/auth', $service->getAuthorizationUri()->getAbsoluteUri());
    }

    /**
     * @covers AbstractService::getAuthorizationUri
     * @covers AbstractService::getAuthorizationEndpoint
     */
    public function testGetAuthorizationUriWithParameters()
    {
        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('http://pieterhordijk.com/auth?foo=bar&baz=beer', $service->getAuthorizationUri(array(
            'foo' => 'bar',
            'baz' => 'beer',
        ))->getAbsoluteUri());
    }

    /**
     * @covers AbstractService::requestAccessToken
     * @covers AbstractService::service
     * @covers AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers AbstractService::getBasicAuthorizationHeaderInfo
     * @covers AbstractService::generateNonce
     * @covers AbstractService::getSignatureMethod
     * @covers AbstractService::getVersion
     * @covers AbstractService::getAccessTokenEndpoint
     * @covers AbstractService::getExtraOAuthHeaders
     * @covers AbstractService::parseAccessTokenResponse
     */
    public function testRequestAccessTokenWithoutSecret()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnCallback(function($endpoint, $array, $headers) {
            Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
        }));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar'));
    }

    /**
     * @covers AbstractService::requestAccessToken
     * @covers AbstractService::service
     * @covers AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers AbstractService::getBasicAuthorizationHeaderInfo
     * @covers AbstractService::generateNonce
     * @covers AbstractService::getSignatureMethod
     * @covers AbstractService::getVersion
     * @covers AbstractService::getAccessTokenEndpoint
     * @covers AbstractService::getExtraOAuthHeaders
     * @covers AbstractService::parseAccessTokenResponse
     */
    public function testRequestAccessTokenWithSecret()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnCallback(function($endpoint, $array, $headers) {
            Assert::assertSame('http://pieterhordijk.com/access', $endpoint->getAbsoluteUri());
        }));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }

    /**
     * @covers AbstractService::request
     * @covers AbstractService::determineRequestUriFromPath
     * @covers AbstractService::service
     * @covers AbstractService::getExtraApiHeaders
     * @covers AbstractService::buildAuthorizationHeaderForAPIRequest
     * @covers AbstractService::getBasicAuthorizationHeaderInfo
     * @covers AbstractService::generateNonce
     * @covers AbstractService::getSignatureMethod
     * @covers AbstractService::getVersion
     */
    public function testRequest()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        //$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('response!', $service->request('/my/awesome/path'));
    }

    /**
     * This test only captures a regression in php 5.3.
     *
     * @covers AbstractService::request
     */
    public function testRequestNonArrayBody()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Mock(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('response!', $service->request('/my/awesome/path', 'GET', 'A text body'));
    }

}
