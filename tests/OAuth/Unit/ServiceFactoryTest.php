<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Chris Heng <bigblah@gmail.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit;

use OAuth\ServiceFactory;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Memory;
use OAuth\Common\Http\Uri\Uri;
use OAuth\OAuth1\Service\Twitter as BaseTwitter;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $factory;

    public function setUp()
    {
        $this->factory = new ServiceFactory();
    }

    public function testRegisterServiceWithNonExistentClass()
    {
        $this->setExpectedException('OAuth\Common\Exception\Exception');
        $this->factory->registerService('foo', 'bar');
    }

    public function testRegisterServiceWithInvalidClass()
    {
        $this->setExpectedException('OAuth\Common\Exception\Exception');
        $this->factory->registerService('foo', 'OAuth\\ServiceFactory');
    }

    public function testCreateOAuth1Service()
    {
        $service = $this->factory->createService('twitter', new Credentials(null, null, null), new Memory());
        $this->assertInstanceOf('OAuth\\OAuth1\\Service\\Twitter', $service);
    }

    public function testCreateOAuth2Service()
    {
        $service = $this->factory->createService('facebook', new Credentials(null, null, null), new Memory());
        $this->assertInstanceOf('OAuth\\OAuth2\\Service\\Facebook', $service);
    }

    public function testCreateNonExistentService()
    {
        $service = $this->factory->createService('foo', new Credentials(null, null, null), new Memory());
        $this->assertNull($service);
    }

    public function testRegisterServiceOverridesDefault()
    {
        $this->factory->registerService('twitter', 'OAuth\\Unit\\Twitter');
        $service = $this->factory->createService('twitter', new Credentials(null, null, null), new Memory());
        $this->assertInstanceOf('OAuth\\Unit\\Twitter', $service);
        $this->assertEquals('https://api.twitter.com/oauth/authorize', (string) $service->getAuthorizationEndpoint());
    }
}

class Twitter extends BaseTwitter
{
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.twitter.com/oauth/authorize');
    }
}
