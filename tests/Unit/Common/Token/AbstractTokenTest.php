<?php

namespace OAuthTest\Unit\Common\Token;

use OAuth\Common\Token\AbstractToken;
use PHPUnit\Framework\TestCase;

class AbstractTokenTest extends TestCase
{
    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        self::assertInstanceOf('\\OAuth\\Common\\Token\\TokenInterface', $token);
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getAccessToken
     */
    public function testGetAccessTokenNotSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        self::assertNull($token->getAccessToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getAccessToken
     */
    public function testGetAccessTokenSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo']);

        self::assertSame('foo', $token->getAccessToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getAccessToken
     * @covers \OAuth\Common\Token\AbstractToken::setAccessToken
     */
    public function testSetAccessToken(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        $token->setAccessToken('foo');

        self::assertSame('foo', $token->getAccessToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getRefreshToken
     */
    public function testGetRefreshToken(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        self::assertNull($token->getRefreshToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getRefreshToken
     */
    public function testGetRefreshTokenSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo', 'bar']);

        self::assertSame('bar', $token->getRefreshToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getRefreshToken
     * @covers \OAuth\Common\Token\AbstractToken::setRefreshToken
     */
    public function testSetRefreshToken(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        $token->setRefreshToken('foo');

        self::assertSame('foo', $token->getRefreshToken());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getExtraParams
     */
    public function testGetExtraParamsNotSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        self::assertSame([], $token->getExtraParams());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getExtraParams
     */
    public function testGetExtraParamsSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo', 'bar', null, ['foo', 'bar']]);

        self::assertEquals(['foo', 'bar'], $token->getExtraParams());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getExtraParams
     * @covers \OAuth\Common\Token\AbstractToken::setExtraParams
     */
    public function testSetExtraParams(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        $token->setExtraParams(['foo', 'bar']);

        self::assertSame(['foo', 'bar'], $token->getExtraParams());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setLifetime
     */
    public function testGetEndOfLifeNotSet(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        self::assertSame(AbstractToken::EOL_UNKNOWN, $token->getEndOfLife());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setLifetime
     */
    public function testGetEndOfLifeZero(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo', 'bar', 0]);

        self::assertSame(AbstractToken::EOL_NEVER_EXPIRES, $token->getEndOfLife());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setLifetime
     */
    public function testGetEndOfLifeNeverExpires(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo', 'bar', AbstractToken::EOL_NEVER_EXPIRES]);

        self::assertSame(AbstractToken::EOL_NEVER_EXPIRES, $token->getEndOfLife());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setLifetime
     */
    public function testGetEndOfLifeNeverExpiresFiveMinutes(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken', ['foo', 'bar', 5 * 60]);

        self::assertSame(time() + (5 * 60), $token->getEndOfLife());
    }

    /**
     * @covers \OAuth\Common\Token\AbstractToken::__construct
     * @covers \OAuth\Common\Token\AbstractToken::getEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setEndOfLife
     * @covers \OAuth\Common\Token\AbstractToken::setLifetime
     */
    public function testSetEndOfLife(): void
    {
        $token = $this->getMockForAbstractClass('\\OAuth\\Common\\Token\\AbstractToken');

        $token->setEndOfLife(10);

        self::assertSame(10, $token->getEndOfLife());
    }
}
