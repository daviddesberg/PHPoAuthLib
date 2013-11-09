<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuthTest\Mocks\OAuth1\Service\Mock;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\AbstractService::__construct
     */
    public function testConstructCorrectInterface()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            array(
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            )
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\AbstractService::__construct
     */
    public function testConstructCorrectParent()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            array(
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
                $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface'),
            )
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\AbstractService::requestRequestToken
     * @covers OAuth\OAuth1\Service\AbstractService::buildAuthorizationHeaderForTokenRequest
     * @covers OAuth\OAuth1\Service\AbstractService::getExtraOAuthHeaders
     * @covers OAuth\OAuth1\Service\AbstractService::parseRequestTokenResponse
     */
    public function testRequestRequestTokenBuildAuthHeaderTokenRequestWithoutParams()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnCallback(function($endpoint, $array, $headers) {
            \PHPUnit_Framework_Assert::assertSame('http://pieterhordijk.com/token', $endpoint->getAbsoluteUri());
        }));

        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationUri
     * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationEndpoint
     */
    public function testGetAuthorizationUriWithoutParameters()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertSame('http://pieterhordijk.com/auth', $service->getAuthorizationUri()->getAbsoluteUri());
    }
}
