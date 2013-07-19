<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Storage\Session;
use OAuth\Unit\Common\Storage\StorageTest;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SessionTest extends StorageTest
{

    public function setUp()
    {
        // set it
        $_SESSION = array();
        $this->storage = new Session();
    }

    public function tearDown()
    {
        // empty
    }

}
