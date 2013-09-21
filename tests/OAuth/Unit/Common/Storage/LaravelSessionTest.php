<?php

/**
 * @category   OAuth
 * @package    Tests
 * @author     Hannes Van De Vreken <vandevreken.hannes@gmail.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Unit\Common\Storage;

use OAuth\Common\Storage\LaravelSession;
use OAuth\Unit\Common\Storage\StorageTest;
use OAuth\OAuth2\Token\StdOAuth2Token;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Facade;
use Illuminate\Session\SessionManager;

// fake container
class FakeApp extends \ArrayObject {}

class LaravelSessionTest extends StorageTest
{
    public function setUp()
    {
        // set it
        $this->storage = new LaravelSession();

        // arrange fake storage
        $this->app = new FakeApp();

        // laravel array session storage
        $session_manager = new SessionManager($this->app);
        $array_driver = $session_manager->driver('array');

        // assing to container
        $this->app['session'] = $array_driver;

        // set fake container
        Facade::setFacadeApplication($this->app);
    }

    public function tearDown()
    {
        // delete
        Session::flush();
        unset($this->storage);
    }
}
