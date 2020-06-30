<?php
/**
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Chris Heng <bigblah@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit;

use OAuth\ServiceFactory;
use PHPUnit\Framework\TestCase;

class ServiceFactoryTest extends TestCase
{
    /**
     * @covers \OAuth\ServiceFactory::setHttpClient
     */
    public function testSetHttpClient(): void
    {
        $factory = new ServiceFactory();

        self::assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            $factory->setHttpClient($this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'))
        );
    }

    /**
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionNonExistentClass(): void
    {
        $this->expectException('\\OAuth\Common\Exception\Exception');

        $factory = new ServiceFactory();
        $factory->registerService('foo', 'bar');
    }

    /**
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionWithClassIncorrectImplementation(): void
    {
        $this->expectException('\\OAuth\Common\Exception\Exception');

        $factory = new ServiceFactory();
        $factory->registerService('foo', 'OAuth\\ServiceFactory');
    }

    /**
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceSuccessOAuth1(): void
    {
        $factory = new ServiceFactory();

        self::assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake')
        );
    }

    /**
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceSuccessOAuth2(): void
    {
        $factory = new ServiceFactory();

        self::assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake')
        );
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV1Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     */
    public function testCreateServiceOAuth1NonRegistered(): void
    {
        $factory = new ServiceFactory();

        $service = $factory->createService(
            'twitter',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth1\\Service\\Twitter', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV1Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testCreateServiceOAuth1Registered(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\OAuth1\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV1Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testCreateServiceOAuth1RegisteredAndNonRegisteredSameName(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('twitter', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'twitter',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\OAuth1\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2NonRegistered(): void
    {
        $factory = new ServiceFactory();

        $service = $factory->createService(
            'facebook',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\\OAuth2\\Service\\Facebook', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2Registered(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredAndNonRegisteredSameName(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('facebook', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'facebook',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV1Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::registerService
     */
    public function testCreateServiceThrowsExceptionOnPassingScopesToV1Service(): void
    {
        $this->expectException('\\OAuth\Common\Exception\Exception');

        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['bar']
        );
    }

    /**
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     */
    public function testCreateServiceNonExistentService(): void
    {
        $factory = new ServiceFactory();

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertNull($service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::registerService
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServicePrefersOauth2(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');
        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        self::assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredWithClassConstantsAsScope(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['FOO']
        );

        self::assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers \OAuth\ServiceFactory::buildV2Service
     * @covers \OAuth\ServiceFactory::createService
     * @covers \OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers \OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredWithCustomScope(): void
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['custom']
        );

        self::assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        self::assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }
}
