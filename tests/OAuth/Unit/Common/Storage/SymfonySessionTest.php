<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

use OAuth\Common\Storage\SymfonySession;
use OAuth\Unit\Common\Storage\StorageTest;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class SymfonySessionTest extends StorageTest
{

    public function setUp()
    {
        // set it
        $session = new Session(new MockArraySessionStorage());
        $this->storage = new SymfonySession($session);
    }

    public function tearDown()
    {
        // delete
        $this->storage->getSession()->clear();
        unset($this->storage);
    }

}
