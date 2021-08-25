<?php

namespace OAuthTest\Unit\Common\Http;

use PHPUnit\Framework\TestCase;

class AbstractClientTest extends TestCase
{
    /**
     * @covers \OAuth\Common\Http\Client\AbstractClient::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $client = $this->getMockForAbstractClass('\\OAuth\\Common\\Http\\Client\\AbstractClient');

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\ClientInterface', $client);
    }

    /**
     * @covers \OAuth\Common\Http\Client\AbstractClient::__construct
     * @covers \OAuth\Common\Http\Client\AbstractClient::setMaxRedirects
     */
    public function testSetMaxRedirects(): void
    {
        $client = $this->getMockForAbstractClass('\\OAuth\\Common\\Http\\Client\\AbstractClient');

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client->setMaxRedirects(10));
        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\ClientInterface', $client->setMaxRedirects(10));
    }

    /**
     * @covers \OAuth\Common\Http\Client\AbstractClient::__construct
     * @covers \OAuth\Common\Http\Client\AbstractClient::setTimeout
     */
    public function testSetTimeout(): void
    {
        $client = $this->getMockForAbstractClass('\\OAuth\\Common\\Http\\Client\\AbstractClient');

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\AbstractClient', $client->setTimeout(25));
        self::assertInstanceOf('\\OAuth\\Common\\Http\\Client\\ClientInterface', $client->setTimeout(25));
    }

    /**
     * @covers \OAuth\Common\Http\Client\AbstractClient::__construct
     * @covers \OAuth\Common\Http\Client\AbstractClient::normalizeHeaders
     */
    public function testNormalizeHeaders(): void
    {
        $client = $this->getMockForAbstractClass('\\OAuth\\Common\\Http\\Client\\AbstractClient');

        $original = [
            'lowercasekey' => 'lowercasevalue',
            'UPPERCASEKEY' => 'UPPERCASEVALUE',
            'mIxEdCaSeKey' => 'MiXeDcAsEvAlUe',
            '31i71casekey' => '31i71casevalue',
        ];

        $goal = [
            'lowercasekey' => 'Lowercasekey: lowercasevalue',
            'UPPERCASEKEY' => 'Uppercasekey: UPPERCASEVALUE',
            'mIxEdCaSeKey' => 'Mixedcasekey: MiXeDcAsEvAlUe',
            '31i71casekey' => '31i71casekey: 31i71casevalue',
        ];

        $original = $client->normalizeHeaders($original);

        self::assertSame($goal, $original);
    }
}
