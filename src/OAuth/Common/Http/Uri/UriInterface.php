<?php
namespace OAuth\Common\Http\Uri;

interface UriInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getScheme();

    /**
     * @abstract
     * @param string $scheme
     */
    public function setScheme($scheme);

    /**
     * @abstract
     * @return string
     */
    public function getHost();

    /**
     * @abstract
     * @param string $host
     */
    public function setHost($host);

    /**
     * @abstract
     * @return int
     */
    public function getPort();

    /**
     * @abstract
     * @param int $port
     */
    public function setPort($port);

    /**
     * @abstract
     * @return string
     */
    public function getPath();

    /**
     * @abstract
     * @param string $path
     */
    public function setPath($path);

    /**
     * @abstract
     * @return string
     */
    public function getQuery();

    /**
     * @abstract
     * @param string $query
     */
    public function setQuery($query);

    /**
     * Adds a param to the query string.
     *
     * @abstract
     * @param string $var
     * @param string $val
     */
    public function addToQuery($var, $val);

    /**
     * @abstract
     * @return string
     */
    public function getFragment();

    /**
     * Should return URI user info, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string
     */
    public function getUserInfo();

    /**
     * @abstract
     * @param string $userInfo
     */
    public function setUserInfo($userInfo);

    /**
     * Should return the URI Authority, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string
     */
    public function getAuthority();

    /**
     * Should return the URI string, masking protected user info data according to rfc3986-3.2.1
     *
     * @abstract
     * @return string the URI string with user protected info masked
     */
    public function __toString();

    /**
     * Should return the URI Authority without masking protected user info data
     *
     * @abstract
     * @return string
     */
    public function getRawAuthority();

    /**
     * Should return the URI user info without masking protected user info data
     *
     * @abstract
     * @return string
     */
    public function getRawUserInfo();

    /**
     * Build the full URI based on all the properties
     *
     * @abstract
     * @return string The full URI without masking user info
     */
    public function getAbsoluteUri();

    /**
     * Build the relative URI based on all the properties
     *
     * @abstract
     * @return string The relative URI
     */
    public function getRelativeUri();

    /**
     * @return bool
     */
    public function hasExplicitTrailingHostSlash();

}
