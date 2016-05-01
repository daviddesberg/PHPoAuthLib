<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\BitBucket;

class BitBucketTest extends AbstractTokenParserTest
{
    
    protected function getClassName()
    {
        return '\OAuth\OAuth1\Service\BitBucket';
    }
    
    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/!api/1.0/oauth/request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/!api/1.0/oauth/authenticate',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/!api/1.0/oauth/access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

}
