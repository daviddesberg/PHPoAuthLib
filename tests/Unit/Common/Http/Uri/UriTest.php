<?php

namespace OAuthTest\Unit\Common\Http\Uri;

use OAuth\Common\Http\Uri\Uri;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     */
    public function testConstructCorrectInterfaceWithoutUri(): void
    {
        $uri = new Uri();

        self::assertInstanceOf('\\OAuth\\Common\\Http\\Uri\\UriInterface', $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testConstructThrowsExceptionOnInvalidUri(): void
    {
        $this->expectException('\\InvalidArgumentException');

        // http://lxr.php.net/xref/PHP_5_4/ext/standard/tests/url/urls.inc#92
        $uri = new Uri('http://@:/');
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testConstructThrowsExceptionOnUriWithoutScheme(): void
    {
        $this->expectException('\\InvalidArgumentException');

        $uri = new Uri('www.pieterhordijk.com');
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getScheme
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetScheme(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('http', $uri->getScheme());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetUserInfo(): void
    {
        $uri = new Uri('http://peehaa@example.com');

        self::assertSame('peehaa', $uri->getUserInfo());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetUserInfoWithPass(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('peehaa:********', $uri->getUserInfo());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetRawUserInfo(): void
    {
        $uri = new Uri('http://peehaa@example.com');

        self::assertSame('peehaa', $uri->getRawUserInfo());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetRawUserInfoWithPass(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('peehaa:pass', $uri->getRawUserInfo());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getHost
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetHost(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('example.com', $uri->getHost());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPort
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPortImplicitHttp(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame(80, $uri->getPort());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPort
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPortImplicitHttps(): void
    {
        $uri = new Uri('https://example.com');

        self::assertSame(443, $uri->getPort());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPort
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPortExplicit(): void
    {
        $uri = new Uri('http://example.com:21');

        self::assertSame(21, $uri->getPort());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPath
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPathNotSupplied(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('/', $uri->getPath());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPath
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPathSlash(): void
    {
        $uri = new Uri('http://example.com/');

        self::assertSame('/', $uri->getPath());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getPath
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetPath(): void
    {
        $uri = new Uri('http://example.com/foo');

        self::assertSame('/foo', $uri->getPath());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getQuery
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetQueryWithParams(): void
    {
        $uri = new Uri('http://example.com?param1=first&param2=second');

        self::assertSame('param1=first&param2=second', $uri->getQuery());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getQuery
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetQueryWithoutParams(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('', $uri->getQuery());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getFragment
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetFragmentExists(): void
    {
        $uri = new Uri('http://example.com#foo');

        self::assertSame('foo', $uri->getFragment());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getFragment
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetFragmentNotExists(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('', $uri->getFragment());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAuthorityWithoutUserInfo(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('example.com', $uri->getAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAuthorityWithoutUserInfoWithExplicitPort(): void
    {
        $uri = new Uri('http://example.com:21');

        self::assertSame('example.com:21', $uri->getAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetAuthorityWithUsernameWithExplicitPort(): void
    {
        $uri = new Uri('http://peehaa@example.com:21');

        self::assertSame('peehaa@example.com:21', $uri->getAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetAuthorityWithUsernameAndPassWithExplicitPort(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com:21');

        self::assertSame('peehaa:********@example.com:21', $uri->getAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetAuthorityWithUsernameAndPassWithoutExplicitPort(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('peehaa:********@example.com', $uri->getAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetRawAuthorityWithoutUserInfo(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('example.com', $uri->getRawAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetRawAuthorityWithoutUserInfoWithExplicitPort(): void
    {
        $uri = new Uri('http://example.com:21');

        self::assertSame('example.com:21', $uri->getRawAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetRawAuthorityWithUsernameWithExplicitPort(): void
    {
        $uri = new Uri('http://peehaa@example.com:21');

        self::assertSame('peehaa@example.com:21', $uri->getRawAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetRawAuthorityWithUsernameAndPassWithExplicitPort(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com:21');

        self::assertSame('peehaa:pass@example.com:21', $uri->getRawAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetRawAuthorityWithUsernameAndPassWithoutExplicitPort(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('peehaa:pass@example.com', $uri->getRawAuthority());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriBare(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testGetAbsoluteUriWithAuthority(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('http://peehaa:pass@example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriWithPath(): void
    {
        $uri = new Uri('http://example.com/foo');

        self::assertSame('http://example.com/foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriWithoutPath(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriWithoutPathExplicitTrailingSlash(): void
    {
        $uri = new Uri('http://example.com/');

        self::assertSame('http://example.com/', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriWithQuery(): void
    {
        $uri = new Uri('http://example.com?param1=value1');

        self::assertSame('http://example.com?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetAbsoluteUriWithFragment(): void
    {
        $uri = new Uri('http://example.com#foo');

        self::assertSame('http://example.com#foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRelativeUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetRelativeUriWithoutPath(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('', $uri->getRelativeUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRelativeUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetRelativeUriWithPath(): void
    {
        $uri = new Uri('http://example.com/foo');

        self::assertSame('/foo', $uri->getRelativeUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getRelativeUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testGetRelativeUriWithExplicitTrailingSlash(): void
    {
        $uri = new Uri('http://example.com/');

        self::assertSame('/', $uri->getRelativeUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringBare(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('http://example.com', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::getRawAuthority
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testToStringWithAuthority(): void
    {
        $uri = new Uri('http://peehaa:pass@example.com');

        self::assertSame('http://peehaa:********@example.com', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringWithPath(): void
    {
        $uri = new Uri('http://example.com/foo');

        self::assertSame('http://example.com/foo', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringWithoutPath(): void
    {
        $uri = new Uri('http://example.com');

        self::assertSame('http://example.com', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringWithoutPathExplicitTrailingSlash(): void
    {
        $uri = new Uri('http://example.com/');

        self::assertSame('http://example.com/', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringWithQuery(): void
    {
        $uri = new Uri('http://example.com?param1=value1');

        self::assertSame('http://example.com?param1=value1', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::__toString
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testToStringWithFragment(): void
    {
        $uri = new Uri('http://example.com#foo');

        self::assertSame('http://example.com#foo', (string) $uri);
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPath
     */
    public function testSetPathEmpty(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPath('');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPath
     */
    public function testSetPathWithPath(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPath('/foo');

        self::assertSame('http://example.com/foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPath
     */
    public function testSetPathWithOnlySlash(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPath('/');

        self::assertSame('http://example.com/', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setQuery
     */
    public function testSetQueryEmpty(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setQuery('');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setQuery
     */
    public function testSetQueryFilled(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setQuery('param1=value1&param2=value2');

        self::assertSame('http://example.com?param1=value1&param2=value2', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::addToQuery
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testAddToQueryAppend(): void
    {
        $uri = new Uri('http://example.com?param1=value1');
        $uri->addToQuery('param2', 'value2');

        self::assertSame('http://example.com?param1=value1&param2=value2', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::addToQuery
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testAddToQueryCreate(): void
    {
        $uri = new Uri('http://example.com');
        $uri->addToQuery('param1', 'value1');

        self::assertSame('http://example.com?param1=value1', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setFragment
     */
    public function testSetFragmentEmpty(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setFragment('');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setFragment
     */
    public function testSetFragmentWithData(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setFragment('foo');

        self::assertSame('http://example.com#foo', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setScheme
     */
    public function testSetSchemeWithEmpty(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setScheme('');

        self::assertSame('://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setScheme
     */
    public function testSetSchemeWithData(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setScheme('foo');

        self::assertSame('foo://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testSetUserInfoEmpty(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setUserInfo('');

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::protectUserInfo
     * @covers \OAuth\Common\Http\Uri\Uri::setUserInfo
     */
    public function testSetUserInfoWithData(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setUserInfo('foo:bar');

        self::assertSame('http://foo:bar@example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPort
     */
    public function testSetPortCustom(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPort('21');

        self::assertSame('http://example.com:21', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPort
     */
    public function testSetPortHttpImplicit(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPort(80);

        self::assertSame('http://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPort
     */
    public function testSetPortHttpsImplicit(): void
    {
        $uri = new Uri('https://example.com');
        $uri->setPort(443);

        self::assertSame('https://example.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPort
     */
    public function testSetPortHttpExplicit(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setPort(443);

        self::assertSame('http://example.com:443', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setPort
     */
    public function testSetPortHttpsExplicit(): void
    {
        $uri = new Uri('https://example.com');
        $uri->setPort(80);

        self::assertSame('https://example.com:80', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::getAbsoluteUri
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     * @covers \OAuth\Common\Http\Uri\Uri::setHost
     */
    public function testSetHost(): void
    {
        $uri = new Uri('http://example.com');
        $uri->setHost('pieterhordijk.com');

        self::assertSame('http://pieterhordijk.com', $uri->getAbsoluteUri());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::hasExplicitTrailingHostSlash
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testHasExplicitTrailingHostSlashTrue(): void
    {
        $uri = new Uri('http://example.com/');

        self::assertTrue($uri->hasExplicitTrailingHostSlash());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::hasExplicitTrailingHostSlash
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testHasExplicitTrailingHostSlashFalse(): void
    {
        $uri = new Uri('http://example.com/foo');

        self::assertFalse($uri->hasExplicitTrailingHostSlash());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::hasExplicitPortSpecified
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testHasExplicitPortSpecifiedTrue(): void
    {
        $uri = new Uri('http://example.com:8080');

        self::assertTrue($uri->hasExplicitPortSpecified());
    }

    /**
     * @covers \OAuth\Common\Http\Uri\Uri::__construct
     * @covers \OAuth\Common\Http\Uri\Uri::hasExplicitPortSpecified
     * @covers \OAuth\Common\Http\Uri\Uri::parseUri
     */
    public function testHasExplicitPortSpecifiedFalse(): void
    {
        $uri = new Uri('http://example.com');

        self::assertFalse($uri->hasExplicitPortSpecified());
    }
}
