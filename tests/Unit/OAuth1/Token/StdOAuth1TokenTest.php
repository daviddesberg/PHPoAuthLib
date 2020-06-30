<?php

namespace OAuthTest\Unit\OAuth1\Token;

use OAuth\OAuth1\Token\StdOAuth1Token;
use PHPUnit\Framework\TestCase;

class StdOAuth1TokenTest extends TestCase
{
    public function testConstructCorrectInterfaces(): void
    {
        $token = new StdOAuth1Token();

        self::assertInstanceOf('\\OAuth\\OAuth1\\Token\\TokenInterface', $token);
        self::assertInstanceOf('\\OAuth\\Common\\Token\\AbstractToken', $token);
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setRequestToken
     */
    public function testSetRequestToken(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setRequestToken('foo'));
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::getRequestToken
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setRequestToken
     */
    public function testGetRequestToken(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setRequestToken('foo'));
        self::assertSame('foo', $token->getRequestToken());
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setRequestTokenSecret
     */
    public function testSetRequestTokenSecret(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setRequestTokenSecret('foo'));
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::getRequestTokenSecret
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setRequestTokenSecret
     */
    public function testGetRequestTokenSecret(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setRequestTokenSecret('foo'));
        self::assertSame('foo', $token->getRequestTokenSecret());
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setAccessTokenSecret
     */
    public function testSetAccessTokenSecret(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setAccessTokenSecret('foo'));
    }

    /**
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::getAccessTokenSecret
     * @covers \OAuth\OAuth1\Token\StdOAuth1Token::setAccessTokenSecret
     */
    public function testGetAccessTokenSecret(): void
    {
        $token = new StdOAuth1Token();

        self::assertNull($token->setAccessTokenSecret('foo'));
        self::assertSame('foo', $token->getAccessTokenSecret());
    }
}
