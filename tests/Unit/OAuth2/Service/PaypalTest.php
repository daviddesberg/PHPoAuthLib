<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Paypal;
use OAuth\Common\Token\TokenInterface;
use OAuthTest\Unit\Common\TestHelper;

class PaypalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://api.paypal.com/v1/identity/openidconnect/tokenservice',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('send')->will($this->returnArgument(2));

        $token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage
        );

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        $this->assertTrue(array_key_exists('Authorization', $headers));
        $this->assertTrue(in_array('Bearer foo', $headers, true));
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse(null)));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnMessage()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('message=some_error')));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnName()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('name=some_error')));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"access_token":"foo","expires_in":"bar"}')));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Paypal::__construct
     * @covers OAuth\OAuth2\Service\Paypal::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}')));

        $service = new Paypal(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
