<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Mailchimp;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Http\Uri\Uri;
use OAuthTest\Unit\Common\TestHelper;

class MailchimpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://login.mailchimp.com/oauth2/authorize',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://login.mailchimp.com/oauth2/token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('send')->will($this->returnArgument(0));

        $token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token');
        $token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            array(),
            new Uri('https://us1.api.mailchimp.com/2.0/')
        );

        $uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url($uri->getAbsoluteUri());

        $this->assertSame('apikey=foo', $absoluteUri['query']);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse(null)));

        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->once())->method('post')->will($this->returnValue(TestHelper::createStringResponse('error=some_error')));

        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $client = $this->getMock('\\Ivory\\HttpAdapter\\HttpAdapterInterface');
        $client->expects($this->at(0))->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"access_token":"foo","expires_in":"bar"}')));
        $client->expects($this->at(1))->method('post')->will($this->returnValue(TestHelper::createStringResponse('{"dc": "us7"}')));

        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
