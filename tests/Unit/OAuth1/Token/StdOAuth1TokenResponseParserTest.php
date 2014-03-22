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

    public function testParseAccessTokenResponseReturnsToken()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar';

        $token = $this->parser->parseAccessTokenResponse($responseBody);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $token);
        $this->assertEquals('foo', $token->getAccessToken());
        $this->assertEquals('bar', $token->getAccessTokenSecret());
    }

    public function testParseAccessTokenResponseThrowsExceptionForInvalidResponse()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Unable to parse response'
        );

        $responseBody = '';

        $this->parser->parseAccessTokenResponse($responseBody);
    }

    public function testParseAccessTokenResponseThrowsExceptionWithErrors()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Error in retrieving token: "Something went wrong"'
        );

        $responseBody = 'error=Something%20went%20wrong';

        $this->parser->parseAccessTokenResponse($responseBody);
    }

    public function testParseAccessTokenResponseAddsExtraParams()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar&baz=biz';

        $token = $this->parser->parseAccessTokenResponse($responseBody);

        $this->assertEquals(array('baz' => 'biz'), $token->getExtraParams());
    }

    public function testParseRequestTokenResponse()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar&oauth_callback_confirmed=true';

        $token = $this->parser->parseRequestTokenResponse($responseBody);

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $token);
        $this->assertEquals('foo', $token->getRequestToken());
        $this->assertEquals('bar', $token->getRequestTokenSecret());
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
