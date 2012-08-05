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

require_once __DIR__ . '/../lib/OAuth/Common/AutoLoader.php';

$autoloader = new \OAuth\Common\AutoLoader('OAuth', dirname(__DIR__) . '/lib');

$autoloader->register();