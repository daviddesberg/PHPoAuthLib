<?php
/**
 * This file sets up the information needed to test the examples in different environments.
 *
 * PHP version 5.4
 *
 * @author     Lusitanian <alusitanian@gmail.com>
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
    ]
];

/**
 * @var callable This will return an instance of the preferred HTTP client used for requests to services
 * @return \OAuth\Common\Http\ArtaxClient
 */
$httpClientProvider = function()
{
    return new OAuth\Common\Http\ArtaxClient();
};