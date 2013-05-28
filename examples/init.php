<?php
/**
 * This file sets up the information needed to test the examples in different environments.
 *
 * PHP version 5.4
 *
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/**
 * @var array A list of all the credentials to be used by the different services in the examples
 */
$servicesCredentials = [
    'bitly' => [
        'key'       => '',
        'secret'    => '',
    ],
    'facebook' => [
        'key'       => '',
        'secret'    => '',
    ],
    'github' => [
        'key'       => '',
        'secret'    => '',
    ],
    'google' => [
        'key'       => '',
        'secret'    => '',
    ],
    'microsoft' => [
        'key'       => '',
        'secret'    => '',
    ],
    'yammer' => [
        'key'       => '',
        'secret'    => ''
    ],
    'soundcloud' => [
        'key'       => '',
        'secret'    => '',
    ],
    'foursquare' => [
        'key'       => '',
        'secret'    => '',
    ],
    'twitter' => [
        'key'       => '',
        'secret'    => '',
    ],
    'fitbit' => [
        'key'       => '',
        'secret'    => '',
    ],
    'instagram' => [
        'key'       => '',
        'secret'    => '',
    ],
    'linkedin' => [
        'key'       => '',
        'secret'    => '',
    ],
    'box' => [
        'key'       => '',
        'secret'    => '',
    ],
    'tumblr' => [
        'key'       => '',
        'secret'    => '',
    ],
];

/** @var $serviceFactory \OAuth\ServiceFactory An OAuth service factory. */
$serviceFactory = new \OAuth\ServiceFactory();
