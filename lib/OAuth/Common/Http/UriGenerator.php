<?php
/**
 * @author Daniel Lowery
 */
namespace OAuth\Common\Http;

use RuntimeException;

class UriGenerator
{

    /**
     * Generates a StdUri from a superglobal $_SERVER array
     *
     * @param array $_server
     * @return StdUri
     */
    public function make($_server, $includeQueryString = true) {
        if ($uri = $this->attemptProxyStyleParse($_server)) {
            return $uri;
        }

        $scheme = $this->detectScheme($_server);
        $host = $this->detectHost($_server);
        $port = $this->detectPort($_server);
        $path = $this->detectPath($_server);
        if( $includeQueryString ) {
            $query = $this->detectQuery($_server);
        } else {
            $query = false;
        }

        $uri = "$scheme://$host";
        $uri.= ($port != 80) ? ":$port" : '';
        $uri.= $path;
        $uri.= $query ? "?$query" : '';

        return new Uri($uri);
    }

    /**
     * @param array $_server
     * @return StdUri
     */
    private function attemptProxyStyleParse($_server) {
        // If the raw HTTP request message arrives with a proxy-style absolute URI in the
        // initial request line, the absolute URI is stored in $_SERVER['REQUEST_URI'] and
        // we only need to parse that.
        if (isset($_server['REQUEST_URI']) && parse_url($_server['REQUEST_URI'], PHP_URL_SCHEME)) {
            return new StdUri($_server['REQUEST_URI']);
        }

        return null;
    }

    /**
     * @param array $_server
     * @return string
     * @throws RuntimeException
     */
    private function detectPath($_server) {
        if (isset($_server['REQUEST_URI'])) {
            $uri = $_server['REQUEST_URI'];
        } elseif (isset($_server['REDIRECT_URL'])) {
            $uri = $_server['REDIRECT_URL'];
        } else {
            throw new RuntimeException("Could not detect URI path from superglobal");
        }

        $queryStr = strpos($uri, '?');
        if ($queryStr !== false) {
            $uri = substr($uri, 0, $queryStr);
        }

        return $uri;
    }

    /**
     * @param array $_server
     * @return string
     */
    private function detectHost(array $_server) {
        return isset($_server['HTTP_HOST']) ? $_server['HTTP_HOST'] : '';
    }

    /**
     * @param array $_server
     * @return string
     */
    private function detectPort(array $_server) {
        return isset($_server['SERVER_PORT']) ? $_server['SERVER_PORT'] : 80;
    }

    /**
     * @param array $_server
     * @return string
     */
    private function detectQuery(array $_server) {
        return isset($_server['QUERY_STRING']) ? $_server['QUERY_STRING'] : '';
    }

    /**
     * Determine URI scheme component from superglobal array
     *
     * When using ISAPI with IIS, the value will be "off" if the request was
     * not made through the HTTPS protocol. As a result, we filter the
     * value to a bool.
     *
     * @param array $_server A superglobal $_SERVER array
     *
     * @return string Returns http or https depending on the URI scheme
     */
    private function detectScheme(array $_server) {
        if (isset($_server['HTTPS'])
            && filter_var($_server['HTTPS'], FILTER_VALIDATE_BOOLEAN)
        ) {
            return 'https';
        } else {
            return 'http';
        }
    }
}
