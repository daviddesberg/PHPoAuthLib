<?php
/**
 * Bootstrap the test cases.
 *
 * PHP version 5.4
 *
 * @category   OAuth
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @author     Lusitanian
 * @copyright  Copyright (c) PHPoAuthLib Team
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace test;
use OAuth\Common\AutoLoader;
require_once __DIR__ . '/../src/OAuth/Common/AutoLoader.php';

$autoloader = new AutoLoader('OAuth', dirname(__DIR__) . '/src');
$autoloader->register();

$artaxloader = new AutoLoader('Artax', dirname(__DIR__) . '/Artax/src' );
$artaxloader->register();