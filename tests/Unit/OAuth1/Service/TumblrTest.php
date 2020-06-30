<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Tumblr;
use PHPUnit\Framework\TestCase;

class TumblrTest extends TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.tumblr.com/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.tumblr.com/oauth/authorize',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.tumblr.com/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=false'
        ));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Tumblr::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        ));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Tumblr::__construct
     * @covers OAuth\OAuth1\Service\Tumblr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Tumblr::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_token=foo&oauth_token_secret=bar'
        ));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Tumblr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }
}
