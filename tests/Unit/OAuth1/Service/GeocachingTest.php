<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Geocaching;

class GeocachingTest extends \PHPUnit_Framework_TestCase
{

    // EndPoints
    const ENDPOINT_STAGING = 'http://staging.geocaching.com/OAuth/oauth.ashx';
    const ENDPOINT_LIVE = 'https://www.geocaching.com/OAuth/oauth.ashx';
    const ENDPOINT_LIVE_MOBILE = 'https://www.geocaching.com/oauth/mobileoauth.ashx';

    // BaseApi
    const BASEAPI_STAGING = 'https://staging.api.groundspeak.com/Live/V6Beta/geocaching.svc/';
    const BASEAPI_LIVE = 'https://api.groundspeak.com/LiveV6/geocaching.svc/';


    protected function getGeocachingInstance() {

        return new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertSame(self::ENDPOINT_LIVE,
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertSame(self::ENDPOINT_LIVE,
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = $this->getGeocachingInstance();
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertSame(self::ENDPOINT_LIVE,
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('notanarray'));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('foo=bar'));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=false'
        ));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Geocaching::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        ));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=bar'));

        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Geocaching::__construct
     * @covers OAuth\OAuth1\Service\Geocaching::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Geocaching::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(
            'oauth_token=foo&oauth_token_secret=bar'
        ));

        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Geocaching(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );
        $service->setEndPoint(self::ENDPOINT_LIVE);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }
}
