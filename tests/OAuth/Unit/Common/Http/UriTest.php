<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@thedesbergs.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Http\Uri\Uri;

class UriTest extends PHPUnit_Framework_TestCase
{
    public function testAllUriComponents()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = new Uri($absoluteUri);
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );
        $this->assertEquals( 6912, $uri->getPort() );
        $this->assertEquals( 'example.com', $uri->getHost() );
        $this->assertEquals( 'https', $uri->getScheme() );
        $this->assertEquals( '/relative/path', $uri->getPath() );
        $this->assertEquals( 'joe:bob', $uri->getRawUserInfo() );
        $this->assertEquals( 'div', $uri->getFragment() );
        $this->assertEquals( 'joe:********', $uri->getUserInfo() );
    }

    public function testFromParts()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = Uri::fromParts('https', 'joe:bob', 'example.com', 6912, '/relative/path', 'user=jon&password=secret', 'div');
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );
        $this->assertEquals( 6912, $uri->getPort() );
        $this->assertEquals( 'example.com', $uri->getHost() );
        $this->assertEquals( 'https', $uri->getScheme() );
        $this->assertEquals( '/relative/path', $uri->getPath() );
        $this->assertEquals( 'joe:bob', $uri->getRawUserInfo() );
        $this->assertEquals( 'div', $uri->getFragment() );
        $this->assertEquals( 'joe:********', $uri->getUserInfo() );
    }

    public function testMutability()
    {
        $absoluteUri = 'https://joe:bob@example.com:6912/relative/path?user=jon&password=secret#div';
        $uri = new Uri($absoluteUri);

        $uri->setPort(443);
        $this->assertEquals( str_replace(':6912', '', $absoluteUri ), $uri->getAbsoluteUri() );

        $uri->setPort(6912);
        $this->assertEquals( $absoluteUri, $uri->getAbsoluteUri() );

        $uri->setHost('github.com');
        $this->assertEquals( str_replace('example.com', 'github.com', $absoluteUri ), $uri->getAbsoluteUri() );
    }
}