<?php
use OAuth\Common\Http\Uri;

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
}