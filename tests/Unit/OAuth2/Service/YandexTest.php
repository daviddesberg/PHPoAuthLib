<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Yandex;
use OAuth\Common\Token\TokenInterface;

class YandexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Yandex::__construct
     * @covers OAuth\OAuth2\Service\Yandex::getAuthorizationUri
     */
    public function testGetAuthorizationUriWithoutAdditionalParams()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));

        $service = new Yandex(
          $credentials,
          $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
          'https://oauth.yandex.ru/authorize?client_id=foo&response_type=code',
          $service->getAuthorizationUri()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Yandex::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.yandex.ru/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }
    
    /**
     * @covers OAuth\OAuth2\Service\Yandex::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.yandex.ru/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }
    
    /**
     * @covers OAuth\OAuth2\Service\Yandex::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue(null));

        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $client,
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Yandex::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('error=some_error'));

        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $client,
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Yandex::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $client,
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Yandex::__construct
     * @covers OAuth\OAuth2\Service\Yandex::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        $client = $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface');
        $client->expects($this->once())->method('retrieveResponse')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

        $service = new Yandex(
          $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
          $client,
          $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

}
