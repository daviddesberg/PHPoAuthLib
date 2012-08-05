<?php
/**
 * URI class. Note that the getters in this class don't return the raw values of the properties, but rather the parsed
 *            values to be used to build the full URI.
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
 * URI class. Note that the getters in this class don't return the raw values of the properties, but rather the parsed
 *            values to be used to build the full URI.
 *
 * @category   OAuth
 * @package    Common
 * @subpackage Http
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 */
class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var boolean
     */
    protected $https;

    /**
     * Constructs the instance
     *
     * @param string            $domain     The domainname
     * @param string            $path       The path of the URI
     * @param string            $protocol   The protocol used by the URI
     * @param string|int        $port       The port used by the URI
     * @param string|boolean    $https      Whether it is an https connection
     *
     * @return string The full URI
     */
    public function __construct($domain, $path, $protocol = 'http', $port = '80', $https = false)
    {
        $this->setDomain($domain);
        $this->setPath($path);
        $this->setProtocol($protocol);
        $this->SetPort($port);
        $this->setHttps($https);
    }

    /**
     * Sets the domainname
     *
     * @param string    $domain The domainname
     *
     * @return void
     */
    protected function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Gets the domainname
     *
     * @return string The domainname
     */
    protected function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the path. The path is the part after the domainname (excluding the optional port and querystring)
     * The path gets normalized (i.e. slashes will be trimmed).
     *
     * @param string    $path   The path of the URI
     *
     * @return void
     */
    protected function setPath($path)
    {
        $this->path = trim($path, '/');
    }

    /**
     * Gets the path
     *
     * @return string The path
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the protocol. Note that the protocol version will be stripped and normalized (e.g. HTTP/1.1 will become http).
     *
     * @param string    $protocol   The protocol used by the URI
     *
     * @return void
     */
    protected function setProtocol($protocol)
    {
        if( false !== ($slashPos =  strpos($protocol, '/') ) ) {
            $this->protocol = strtolower( substr($protocol, 0, $slashPos) );
        } else {
            $this->protocol = $protocol;
        }
    }

    /**
     * Gets the protocol
     *
     * @return string The protocol
     */
    protected function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Sets the port
     *
     * @param string|int    $port   The port used by the URI
     *
     * @return void
     */
    protected function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * Gets the port. Note that the
     *
     * @return string The port as used in the URI when it is not a standard HTTP port prepended by the port delimiter (:).
     */
    protected function getPort()
    {
        if (!in_array( $this->port, [80, 443] ) ) {
            return ':' . $this->port;
        }

        return '';
    }

    /**
     * Sets whether the URI is an https URI
     *
     * @param string|boolean    $https  Whether it is an https connection
     *
     * @return void
     */
    protected function setHttps($https)
    {
        if( is_string($https) ) {
            $this->https = ($https === 'on') ? true : false;
        } elseif( is_bool($https) ) {
            $this->https = $https;
        } elseif( null === $https )  {
            $this->https = false;
        } else {
            throw new \UnexpectedValueException('Expected boolean or string, instead got ' . gettype($https) );
        }
    }

    /**
     * Gets the https suffix
     *
     * @return string|null https suffix (s) of the protocol when the URI is https
     */
    protected function getHttps()
    {
        if ($this->https) {
            return 's';
        }
    }

    /**
     * Build the relative URI based on all the properties
     *
     * @return string The relative URI
     */
    public function getRelativeUri()
    {
        return '/' . $this->getPath();
    }

    /**
     * Build the full URI based on all the properties
     *
     * @return string The full URI
     */
    public function getAbsoluteUri()
    {
        return $this->getProtocol() . $this->getHttps() . '://' . $this->getDomain() . $this->getPort() . $this->getRelativeUri();
    }
}