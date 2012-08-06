<?php
use OAuth\Common\Storage\Memory;
use OAuth\OAuth2\Token\StdOAuth2Token;

class MemoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Pretty pointless, check that it actually keeps the token.
     */
    public function testStoresInMemory()
    {
        // create sample token
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, ['extra' => 'param'] );
        $memory = new Memory();
        $memory->storeAccessToken( $token );

        $this->assertEquals( 'param', $memory->retrieveAccessToken()->getExtraParams()['extra'] );
        $this->assertEquals( 'access', $memory->retrieveAccessToken()->getAccessToken() );
        unset($memory);
    }

    /**
     * Verifies proper behavior upon attempting to retrieve a nonexistent token.
     */
    public function testException()
    {
        $memory = new Memory();
        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $nonExistentToken = $memory->retrieveAccessToken();
    }
}