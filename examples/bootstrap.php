<?php
/**
 * Bootstrap file for the different examples. This file should be included at the top of al examples to easiliy load
 * the library files and setup the defaults for the examples.
 *
 * PHP version 5.4
 *
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * Bootstrap the library
 */
require_once '/../lib/OAuth/bootstrap.php';

/**
 * Setup error reporting
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Setup the timezone
 */
ini_set('date.timezone', 'Europe/Amsterdam');

/**
 * Create a new instance of the URI class with the current URI
 */
$currentUri = new OAuth\Common\Http\Uri(
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI'],
    $_SERVER['SERVER_PROTOCOL'],
    $_SERVER['SERVER_PORT'],
    empty($_SERVER['HTTPS']) ? null : $_SERVER['HTTPS']
);