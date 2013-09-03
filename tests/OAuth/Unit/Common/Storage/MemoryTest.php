<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\Memory;
use OAuth\Unit\Common\Storage\StorageTest;

class MemoryTest extends StorageTest
{
    public function setUp()
    {
        // set it
        $this->storage = new Memory();
    }

    public function tearDown()
    {
        // delete
        unset($this->storage);
    }
}
