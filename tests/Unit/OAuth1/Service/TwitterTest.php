<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Twitter;

class TwitterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.twitter.com/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertTrue(
            in_array(
                strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()), 
                array(\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE)
            )
        );

        $service->setAuthorizationEndpoint( \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE );

        $this->assertTrue(
            in_array(
                strtolower($service->getAuthorizationEndpoint()->getAbsoluteUri()), 
                array(\OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHENTICATE, \OAuth\OAuth1\Service\Twitter::ENDPOINT_AUTHORIZE)
            )
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::setAuthorizationEndpoint
     */
    public function testSetAuthorizationEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Exception\\Exception');

        $service->setAuthorizationEndpoint('foo');
    }

    /**
     * @covers OAuth\OAuth1\Service\Twitter::__construct
     * @covers OAuth\OAuth1\Service\Twitter::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Twitter(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.twitter.com/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }
}
