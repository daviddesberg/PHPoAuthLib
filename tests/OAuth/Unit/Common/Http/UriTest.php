<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Http\Uri\UriFactory;

class UriTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var UriFactory
     */
    protected $uriFactory;

    public function setUp()
    {
        $this->uriFactory = new UriFactory();
    }

    public function tearDown()
    {
        unset($this->uriFactory);
    }

    /**
     * Test that the Uri's parts get parsed correctly when constructued using an absolute uri.
     */
    public function testFromAbsolute()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = $this->uriFactory->createFromAbsolute($absoluteUri);
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );
        $this->assertEquals( 6912, $uri->getPort() );
        $this->assertEquals( 'example.com', $uri->getHost() );
        $this->assertEquals( 'https', $uri->getScheme() );
        $this->assertEquals( '/relative/path', $uri->getPath() );
        $this->assertEquals( 'joe:bob', $uri->getRawUserInfo() );
        $this->assertEquals( 'div', $uri->getFragment() );
        $this->assertEquals( 'joe:********', $uri->getUserInfo() );
        $this->assertEquals( 'joe:********@example.com:6912', $uri->getAuthority() );
        $this->assertEquals( 'joe:bob@example.com:6912', $uri->getRawAuthority() );

        $this->assertEquals( 'https://joe:********@example.com:6912/relative/path?user=jon&password=secret#div', ( string ) $uri );
        $this->assertEquals( '/relative/path', $uri->getRelativeUri() );
    }

    /**
     * Test the creation of a Uri from parts.
     */
    public function testFromParts()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = $this->uriFactory->createFromParts('https', 'joe:bob', 'example.com', 6912, '/relative/path', 'user=jon&password=secret', 'div');
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );
        $this->assertEquals( 6912, $uri->getPort() );
        $this->assertEquals( 'example.com', $uri->getHost() );
        $this->assertEquals( 'https', $uri->getScheme() );
        $this->assertEquals( '/relative/path', $uri->getPath() );
        $this->assertEquals( 'joe:bob', $uri->getRawUserInfo() );
        $this->assertEquals( 'div', $uri->getFragment() );
        $this->assertEquals( 'joe:********', $uri->getUserInfo() );
        $this->assertEquals( 'joe:********@example.com:6912', $uri->getAuthority() );
        $this->assertEquals( 'joe:bob@example.com:6912', $uri->getRawAuthority() );
    }

    /**
     * Test the mutability of the Uri class.
     */
    public function testMutability()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = $this->uriFactory->createFromAbsolute($absoluteUri);

        $uri->setPort(443);
        $this->assertEquals( str_replace(':6912', '', $absoluteUri ), $uri->getAbsoluteUri() );

        $uri->setPort(6912);
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );

        $uri->setHost('github.com');
        $this->assertEquals( str_replace('example.com', 'github.com', $absoluteUri ), $uri->getAbsoluteUri() );
    }

    public function testExceptionOnBadUri()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->uriFactory->createFromAbsolute('sajifodasiojfoisja390bafj#($)');
    }

    /**
     * Test the creation of a Uri from a super-global array.
     */
    public function testSuperGlobals()
    {
        $serverArray = array(
            'REDIRECT_URL' => '/some/relative/path',
            'HTTPS'        => false,
            'HTTP_HOST'    => 'example.com',
            'QUERY_STRING' => 'test=param'
        );

        $uri = $this->uriFactory->createFromSuperGlobalArray($serverArray);

        $this->assertEquals( $uri->getAbsoluteUri(), 'http://example.com/some/relative/path?test=param' );
        $this->assertEquals( $uri->getHost(), 'example.com' );
        $this->assertEquals( $uri->getPort(), 80 );
        $this->assertEquals( $uri->getScheme(), 'http' );
        $this->assertEquals( $uri->getPath(), '/some/relative/path' );
        $this->assertEquals( $uri->getFragment(), '' );
    }


    /**
     * Test the creation of a Uri from a super-global array.
     */
    public function testSuperGlobalsWithNonStandardPort()
    {
        $serverArray = array(
            'REDIRECT_URL' => '/some/relative/path',
            'HTTPS'        => false,
            'HTTP_HOST'    => 'example.com:7000',
            'SERVER_PORT'  => 7000,
            'QUERY_STRING' => 'test=param'
        );

        $uri = $this->uriFactory->createFromSuperGlobalArray($serverArray);

        $this->assertEquals( $uri->getAbsoluteUri(), 'http://example.com:7000/some/relative/path?test=param' );
        $this->assertEquals( $uri->getHost(), 'example.com' );
        $this->assertEquals( $uri->getPort(), 7000 );
        $this->assertEquals( $uri->getScheme(), 'http' );
        $this->assertEquals( $uri->getPath(), '/some/relative/path' );
        $this->assertEquals( $uri->getFragment(), '' );
    }
}
