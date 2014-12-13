<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Vkontakte;
use OAuthTest\Unit\Common\TestHelper;

class VkontakteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.vk.com/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.vk.com/access_token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse(null)));

        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('error=some_error')));

        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"access_token":"foo","expires_in":"bar"}')));

        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}')));

        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
