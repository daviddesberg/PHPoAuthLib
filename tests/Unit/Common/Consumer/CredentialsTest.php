<?php

namespace OAuth\Unit\Common\Consumer;

use OAuth\Common\Consumer\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * Class CredentialsTest
 * @coversDefaultClass Credentials
 */
class CredentialsTest extends TestCase
{
    public function testConstructCorrectInterface()
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        $this->assertInstanceOf('\\OAuth\\Common\\Consumer\\CredentialsInterface', $credentials);
    }

    public function testGetConsumerId()
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        $this->assertSame('foo', $credentials->getConsumerId());
    }

    public function testGetConsumerSecret()
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        $this->assertSame('bar', $credentials->getConsumerSecret());
    }

    public function testGetCallbackUrl()
    {
        $credentials = new Credentials('foo', 'bar', 'baz');

        $this->assertSame('baz', $credentials->getCallbackUrl());
    }
}
