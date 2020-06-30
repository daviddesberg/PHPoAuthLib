<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Flickr;
use PHPUnit\Framework\TestCase;

class FlickrTest extends TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.flickr.com/services/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.flickr.com/services/oauth/authorize',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.flickr.com/services/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=false'
        ));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        ));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->expectException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::__construct
     * @covers OAuth\OAuth1\Service\Flickr::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Flickr::parseAccessTokenResponse
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

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }

    /**
     * @covers OAuth\OAuth1\Service\Flickr::request
     */
    public function testRequest()
    {
        $client = $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('response!'));

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Flickr(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('response!', $service->request('/my/awesome/path'));
    }
}
