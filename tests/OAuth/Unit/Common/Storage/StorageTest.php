<?php namespace OAuth\Unit\Common\Storage;
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use \OAuth\Common\Storage\Memory;
use \OAuth\OAuth2\Token\StdOAuth2Token;

abstract class StorageTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;

    /**
     * Check that the token gets properly stored.
     */
    public function testStorage()
    {
        // variables
        $service_1 = 'Facebook';
        $service_2 = 'Foursquare';

        // create sample token
        $token_1 = new StdOAuth2Token('access_1', 'refresh_1', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        $token_2 = new StdOAuth2Token('access_2', 'refresh_2', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        
        $this->storage->storeAccessToken($service_1, $token_1);
        $this->storage->storeAccessToken($service_2, $token_2);

        $extraParams = $this->storage->retrieveAccessToken($service_1)->getExtraParams();
        $this->assertEquals('param', $extraParams['extra'] );
        $this->assertEquals('access_1', $this->storage->retrieveAccessToken($service_1)->getAccessToken() );
        $this->assertEquals('access_2', $this->storage->retrieveAccessToken($service_2)->getAccessToken() );

        // delete
        $this->storage->clearToken();
    }

    /**
     * Verifies proper behavior upon attempting to retrieve a nonexistent token.
     */
    public function testException()
    {
        // variable:
        $service = 'Facebook';

        // make sure nothing is set.
        $this->storage->clearToken();

        // test
        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $nonExistentToken = $this->storage->retrieveAccessToken($service);
    }

    /**
     * Check that the token gets properly deleted.
     */
    public function testStorageClears()
    {
        // service
        $service = 'Facebook';

        // create sample token
        $token = new StdOAuth2Token('access', 'refresh', StdOAuth2Token::EOL_NEVER_EXPIRES, array('extra' => 'param') );
        
        // save first
        $this->storage->storeAccessToken($service, $token);

        // perform not-null test
        $this->assertNotNull($this->storage->retrieveAccessToken($service));

        $this->storage->clearToken();

        $this->setExpectedException('OAuth\Common\Storage\Exception\TokenNotFoundException');
        $this->storage->retrieveAccessToken($service);
    }
}
