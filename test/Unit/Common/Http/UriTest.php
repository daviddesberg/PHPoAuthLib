<?php
use OAuth\Common\Http\Uri;

class UriTest extends PHPUnit_Framework_TestCase
{
    public function getDefaultsForInstance()
    {
        return
        [
            'domain'    => 'example.com',
            'path'      => '/some/nested/path',
            'protocol'  => 'HTTP/1.1',
            'port'      => 80,
            'https'     => null,
        ];
    }

    public function testGetRelativeUriWithSlashesAroundPath()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            '/some/path/enclosed/in/slashes/',
            $default['protocol'],
            $default['port'],
            $default['https']
        );

        $this->assertEquals('/some/path/enclosed/in/slashes', $uri->getRelativeUri());
    }

    public function testGetRelativeUriWithOnlyASlash()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            '/',
            $default['protocol'],
            $default['port'],
            $default['https']
        );

        $this->assertEquals('/', $uri->getRelativeUri());
    }

    public function testGetAbsoluteUriWithHttps()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            $default['path'],
            $default['protocol'],
            $default['port'],
            'on'
        );

        $this->assertEquals('https://example.com/some/nested/path', $uri->getAbsoluteUri());
    }

    public function testGetAbsoluteUriWithNonStandardPort()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            $default['path'],
            $default['protocol'],
            81,
            $default['https']
        );

        $this->assertEquals('http://example.com:81/some/nested/path', $uri->getAbsoluteUri());
    }

    public function testGetAbsoluteUriWithOnlyRequiredParameters()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            $default['path']
        );

        $this->assertEquals('http://example.com/some/nested/path', $uri->getAbsoluteUri());
    }

    public function testGetAbsoluteUriWithAllOptionalParametersDifferent()
    {
        $default = $this->getDefaultsForInstance();

        $uri = new Uri(
            $default['domain'],
            $default['path'],
            'ftp',
            '1337',
            'on'
        );

        $this->assertEquals('ftps://example.com:1337/some/nested/path', $uri->getAbsoluteUri());
    }
}