<?php

namespace OAuth\Unit\Common\Consumer;

use OAuth\Common\Consumer\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * Class CredentialsTest.
 *
 * @coversDefaultClass \Credentials
 */
class CredentialsTest extends TestCase
{
    public function testConstructCorrectInterface(): void
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        self::assertInstanceOf('\\OAuth\\Common\\Consumer\\CredentialsInterface', $credentials);
    }

    public function testGetConsumerId(): void
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        self::assertSame('foo', $credentials->getConsumerId());
    }

    public function testGetConsumerSecret(): void
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        self::assertSame('bar', $credentials->getConsumerSecret());
    }

    public function testGetCallbackUrl(): void
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        self::assertSame('baz', $credentials->getCallbackUrl());
    }
}
