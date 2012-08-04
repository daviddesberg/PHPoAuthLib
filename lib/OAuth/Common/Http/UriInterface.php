<?php
/**
 * URI interface. All classes which build URI's should implement this interface
 *
 * PHP version 5.4
 *
 * @category   OAuth
 * @package    Common
 * @subpackage Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2012 Pieter Hordijk
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\Common\Http;

/**
 * URI interface. All classes which build URI's should implement this interface
 *
 * @category   OAuth
 * @package    Common
 * @subpackage Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
interface UriInterface
{
    /**
     * Build the full URI based on all the properties
     *
     * @return string The full URI
     */
    public function getAbsoluteUri();

    /**
     * Build the relative URI based on all the properties
     *
     * @return string The relative URI
     */
    public function getRelativeUri();
}