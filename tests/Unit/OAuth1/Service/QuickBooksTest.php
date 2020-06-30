<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth1\Service\QuickBooks;
use PHPUnit\Framework\TestCase;

class QuickBooksTest extends TestCase
{
    public function testConstructCorrectInterfaceWithoutCustomUri(): void
    {
        $service = $this->getQuickBooks();
        self::assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\ServiceInterface',
            $service
        );
    }

    public function testConstructCorrectInstanceWithoutCustomUri(): void
    {
        $service = $this->getQuickBooks();
        self::assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            $service
        );
    }

    public function testConstructCorrectInstanceWithCustomUri(): void
    {
        $service = new QuickBooks(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->createMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        self::assertInstanceOf(
            '\\OAuth\\OAuth1\\Service\\AbstractService',
            $service
        );
    }

    public function testGetRequestTokenEndpoint(): void
    {
        $service = $this->getQuickBooks();
        self::assertSame(
            'https://oauth.intuit.com/oauth/v1/get_request_token',
            $service->getRequestTokenEndpoint()->getAbsoluteUri()
        );
    }

    public function testGetAuthorizationEndpoint(): void
    {
        $service = $this->getQuickBooks();
        self::assertSame(
            'https://appcenter.intuit.com/Connect/Begin',
            $service->getAuthorizationEndpoint()->getAbsoluteUri()
        );
    }

    public function testGetAccessTokenEndpoint(): void
    {
        $service = $this->getQuickBooks();
        self::assertSame(
            'https://oauth.intuit.com/oauth/v1/get_access_token',
            $service->getAccessTokenEndpoint()->getAbsoluteUri()
        );
    }

    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse(): void
    {
        $this->expectException(\OAuth\Common\Http\Exception\TokenResponseException::class);
        $this->expectExceptionMessage('Error in retrieving token.');

        $client = $this->getClientInterfaceMockThatReturns(null);
        $service = $this->getQuickBooks($client);
        $service->requestRequestToken();
    }

    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray(): void
    {
        $this->expectException(\OAuth\Common\Http\Exception\TokenResponseException::class);
        $this->expectExceptionMessage('Error in retrieving token.');

        $client = $this->getClientInterfaceMockThatReturns('notanarray');
        $service = $this->getQuickBooks($client);
        $service->requestRequestToken();
    }

    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet(): void
    {
        $this->expectException(\OAuth\Common\Http\Exception\TokenResponseException::class);
        $this->expectExceptionMessage('Error in retrieving token.');

        $client = $this->getClientInterfaceMockThatReturns('foo=bar');
        $service = $this->getQuickBooks($client);
        $service->requestRequestToken();
    }

    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue(): void
    {
        $this->expectException(\OAuth\Common\Http\Exception\TokenResponseException::class);
        $this->expectExceptionMessage('Error in retrieving token.');

        $client = $this->getClientInterfaceMockThatReturns(
            'oauth_callback_confirmed=false'
        );
        $service = $this->getQuickBooks($client);
        $service->requestRequestToken();
    }

    public function testParseRequestTokenResponseValid(): void
    {
        $client = $this->getClientInterfaceMockThatReturns(
            'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
        );
        $service = $this->getQuickBooks($client);
        self::assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $service->requestRequestToken()
        );
    }

    public function testParseAccessTokenResponseThrowsExceptionOnError(): void
    {
        $this->expectException(\OAuth\Common\Http\Exception\TokenResponseException::class);
        $this->expectExceptionMessage('Error in retrieving token: "bar"');

        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $service = $this->getQuickBooksForRequestingAccessToken(
            $token,
            'error=bar'
        );

        $service->requestAccessToken('foo', 'bar', $token);
    }

    public function testParseAccessTokenResponseValid(): void
    {
        $token = $this->createMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $service = $this->getQuickBooksForRequestingAccessToken(
            $token,
            'oauth_token=foo&oauth_token_secret=bar'
        );

        self::assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $service->requestAccessToken('foo', 'bar', $token)
        );
    }

    protected function getQuickBooks(
        ?ClientInterface $client = null,
        ?TokenStorageInterface $storage = null
    ) {
        if (!$client) {
            $client = $this->createMock(
                '\\OAuth\\Common\\Http\\Client\\ClientInterface'
            );
        }

        if (!$storage) {
            $storage = $this->createMock(
                '\\OAuth\\Common\\Storage\\TokenStorageInterface'
            );
        }

        return new QuickBooks(
            $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $storage,
            $this->createMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );
    }

    protected function getQuickBooksForRequestingAccessToken(
        TokenInterface $token,
        $response
    ) {
        $client = $this->getClientInterfaceMockThatReturns($response);
        $storage = $this->createMock(
            '\\OAuth\\Common\\Storage\\TokenStorageInterface'
        );
        $storage->expects(self::any())
            ->method('retrieveAccessToken')
            ->willReturn($token);

        return $this->getQuickBooks($client, $storage);
    }

    protected function getClientInterfaceMockThatReturns($returnValue)
    {
        $client = $this->createMock(
            '\\OAuth\\Common\\Http\\Client\\ClientInterface'
        );
        $client->expects(self::once())
            ->method('retrieveResponse')
            ->willReturn($returnValue);

        return $client;
    }
}
