<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

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
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        $memory = new Memory();
        $memory->storeAccessToken( $token );

        $extraParams = $memory->retrieveAccessToken()->getExtraParams();
        $this->assertEquals( 'param', $extraParams['extra'] );
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

    /**
    * Check that we can delete tokens that are in memory
    */
    public function testStorageClears()
    {
        // create sample token
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        $memory = new Memory();
        $memory->storeAccessToken( $token );
        $this->assertNotNull($memory->retrieveAccessToken());

        $memory->clearToken();

        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $memory->retrieveAccessToken();

        unset($memory);
    }
}
