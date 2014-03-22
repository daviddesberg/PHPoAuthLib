<?php

namespace OAuthTest\Unit\OAuth1\Token;

use OAuth\OAuth1\Token\StdOauth1AccessTokenResponseParser;

class StdOauth1AccessTokenResponseParserTest extends \PHPUnit_Framework_TestCase
{
    private $parser;


    protected function setUp()
    {
        parent::setUp();

        $this->parser = new StdOauth1AccessTokenResponseParser();
    }

    public function testParseReturnsToken()
    {
        $responseBody = 'oauth_token=foo&oauth_token_secret=bar';

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $this->parser->parse($responseBody)
        );
    }

    public function testParseThrowsExceptionForInvalidResponse()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Unable to parse response'
        );

        $responseBody = '';

        $this->parser->parse($responseBody);
    }

    public function testParseThrowsExceptionWithErrors()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Error in retrieving token: "Something went wrong"'
        );

        $responseBody = 'error=Something%20went%20wrong';

        $this->parser->parse($responseBody);
    }

    public function testValidateRequestTokenResponse()
    {
        $this->setExpectedException(
            '\\OAuth\\Common\\Http\\Exception\\TokenResponseException',
            'Error in retrieving token'
        );

        $responseBody = '';

        $this->parser->validateRequestTokenResponse($responseBody);
    }
}
