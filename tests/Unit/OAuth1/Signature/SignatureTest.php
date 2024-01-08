<?php

namespace OAuthTest\Unit\OAuth1\Signature;

use OAuth\OAuth1\Signature\Signature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     */
    public function testConstructCorrectInterface(): void
    {
        $signature = new Signature($this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        self::assertInstanceOf('\\OAuth\\OAuth1\\Signature\\SignatureInterface', $signature);
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     */
    public function testSetHashingAlgorithm(): void
    {
        $signature = new Signature($this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        self::assertNull($signature->setHashingAlgorithm('foo'));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testSetTokenSecret(): void
    {
        $signature = new Signature($this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        self::assertNull($signature->setTokenSecret('foo'));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureBareUri(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/foo');

        self::assertSame('uoCpiII/Lg/cPiF0XrU2pj4eGFQ=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureWithQueryString(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/foo');

        self::assertSame('LxtD+WjJBRppIUvEI79iQ7I0hSo=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureWithAuthority(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('peehaa:pass');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/foo');

        self::assertSame('MHvkRndIntLrxiPkjkiCNsMEqv4=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureWithBarePathNonExplicitTrailingHostSlash(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('peehaa:pass');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/');
        $uri->expects(self::any())
            ->method('hasExplicitTrailingHostSlash')
            ->willReturn(false);

        self::assertSame('iFELDoiI5Oj9ixB3kHzoPvBpq0w=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureWithBarePathWithExplicitTrailingHostSlash(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('peehaa:pass');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/');
        $uri->expects(self::any())
            ->method('hasExplicitTrailingHostSlash')
            ->willReturn(true);

        self::assertSame('IEhUsArSTLvbQ3QYr0zzn+Rxpjg=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureNoTokenSecretSet(): void
    {
        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('peehaa:pass');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/');
        $uri->expects(self::any())
            ->method('hasExplicitTrailingHostSlash')
            ->willReturn(true);

        self::assertSame('YMHF7FYmLq7wzGnnHWYtd1VoBBE=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers \OAuth\OAuth1\Signature\Signature::__construct
     * @covers \OAuth\OAuth1\Signature\Signature::getSignature
     * @covers \OAuth\OAuth1\Signature\Signature::getSigningKey
     * @covers \OAuth\OAuth1\Signature\Signature::hash
     * @covers \OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers \OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testGetSignatureThrowsExceptionOnUnsupportedAlgo(): void
    {
        $this->expectException('\\OAuth\\OAuth1\\Signature\\Exception\\UnsupportedHashAlgorithmException');

        $credentials = $this->createMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects(self::any())
            ->method('getConsumerSecret')
            ->willReturn('foo');

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('UnsupportedAlgo');

        $uri = $this->createMock('\\OAuth\\Common\\Http\\Uri\\UriInterface');
        $uri->expects(self::any())
            ->method('getQuery')
            ->willReturn('param1=value1');
        $uri->expects(self::any())
            ->method('getScheme')
            ->willReturn('http');
        $uri->expects(self::any())
            ->method('getRawAuthority')
            ->willReturn('peehaa:pass');
        $uri->expects(self::any())
            ->method('getPath')
            ->willReturn('/');
        $uri->expects(self::any())
            ->method('hasExplicitTrailingHostSlash')
            ->willReturn(true);

        $signature->getSignature($uri, ['pee' => 'haa']);
    }
}
