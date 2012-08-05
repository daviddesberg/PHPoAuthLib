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
 * @author     Daniel Lowery
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
 * @author     Daniel Lowery
 */
interface UriInterface
{
    /**
     * @abstract
     * @return string
     */
    function getScheme();

    /**
     * @abstract
     * @return string
     */
    function getHost();

    /**
     * @abstract
     * @return int
     */
    function getPort();

    /**
     * @abstract
     * @return string
     */
    function getPath();

    /**
     * @abstract
     * @return string
     */
    function getQuery();

    /**
     * @abstract
     * @return string
     */
    function getFragment();

    /**
     * Should return URI user info, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string
     */
    function getUserInfo();

    /**
     * Should return the URI Authority, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string
     */
    function getAuthority();

    /**
     * Should return the URI string, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string the URI string with user protected info masked
     */
    function __toString();

    /**
     * Should return the URI Authority without masking protected user info data
     *
     * @abstract
     * @return string
     */
    function getRawAuthority();

    /**
     * Should return the URI user info without masking protected user info data
     *
     * @abstract
     * @return string
     */
    function getRawUserInfo();

    /**
     * Build the full URI based on all the properties
     *
     * @abstract
     * @return string The full URI without masking user info
     */
    function getAbsoluteUri();

    /**
     * Build the relative URI based on all the properties
     *
     * @abstract
     * @return string The relative URI
     */
    function getRelativeUri();

}