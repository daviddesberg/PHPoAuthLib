<?php

namespace OAuthTest\Unit\OAuth1\Token;

use OAuth\OAuth1\Token\StdOAuth1TokenResponseParser;

class StdOauth1TokenResponseParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;


    protected function setUp()
    {
        parent::setUp();

        $this->parser = new StdOAuth1TokenResponseParser();
    }

    public function testParseReturnsToken()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar';

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $this->parser->parseAccessTokenResponse($responseBody)
        );
    }

    public function testParseThrowsExceptionForInvalidResponse()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Unable to parse response'
        );

        $responseBody = '';

        $this->parser->parseAccessTokenResponse($responseBody);
    }

    public function testParseThrowsExceptionWithErrors()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Error in retrieving token: "Something went wrong"'
        );

        $responseBody = 'error=Something%20went%20wrong';

        $this->parser->parseAccessTokenResponse($responseBody);
    }

    public function testParseRequestTokenResponse()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar&oauth_callback_confirmed=true';

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $this->parser->parseRequestTokenResponse($responseBody)
        );
    }

    public function testParseRequestTokenResponseThrowsException()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Error in retrieving token'
        );

        $responseBody = '';

        $this->parser->parseRequestTokenResponse($responseBody);
    }
}
