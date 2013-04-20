<?php
namespace OAuth\Common\Http\Uri;

interface UriInterface
{
    /**
     * @abstract
     * @return string
     */
    function getScheme();

    /**
     * @abstract
     * @param string $scheme
     */
    function setScheme($scheme);

    /**
     * @abstract
     * @return string
     */
    function getHost();

    /**
     * @abstract
     * @param string $host
     */
    function setHost($host);

    /**
     * @abstract
     * @return int
     */
    function getPort();

    /**
     * @abstract
     * @param int $port
     */
    function setPort($port);

    /**
     * @abstract
     * @return string
     */
    function getPath();

    /**
     * @abstract
     * @param string $path
     */
    function setPath($path);

    /**
     * @abstract
     * @return string
     */
    function getQuery();

    /**
     * @abstract
     * @param string $query
     */
    function setQuery($query);

    /**
     * Adds a param to the query string.
     *
     * @abstract
     * @param string $var
     * @param string $val
     */
    function addToQuery($var, $val);

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
     * @abstract
     * @param string $userInfo
     */
    function setUserInfo($userInfo);

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

    /**
     * @return bool
     */
    function hasExplicitTrailingHostSlash();

}
