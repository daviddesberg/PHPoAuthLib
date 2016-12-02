<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\QuickBooks;

class QuickBooksTest extends AbstractTokenParserTest
{
    
    protected function getClassName()
    {
        return '\OAuth\OAuth1\Service\QuickBooks';
    }
    
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = $this->getQuickBooks();
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\ServiceInterface',
            $service
        );
    }

    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = $this->getQuickBooks();
        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            $service
        );
    }

    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new QuickBooks(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            $service
        );
    }

    public function testGetRequestTokenEndpoint()
    {
        $service = $this->getQuickBooks();
        $this->assertSame(
            'https://oauth.intuit.com/oauth/v1/get_request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    public function testGetAuthorizationEndpoint()
    {
        $service = $this->getQuickBooks();
        $this->assertSame(
            'https://appcenter.intuit.com/Connect/Begin',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    public function testGetAccessTokenEndpoint()
    {
        $service = $this->getQuickBooks();
        $this->assertSame(
            'https://oauth.intuit.com/oauth/v1/get_access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    protected function getQuickBooks(
        ClientInterface $client = null,
        TokenStorageInterface $storage = null
    )
    {
        if (!$client) {
            $client = $this->getMock(
                '\\OAuth\\Common\\Http\\Client\\ClientInterface'
            );
        }

        if (!$storage) {
            $storage = $this->getMock(
                '\\OAuth\\Common\\Storage\\TokenStorageInterface'
            );
        }

        return new QuickBooks(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );
    }
}
