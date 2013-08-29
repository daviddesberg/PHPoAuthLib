<?php
namespace OAuth\Common\Http\Client;

/**
 * Abstract HTTP client
 */
abstract class AbstractClient implements ClientInterface
{
    protected $maxRedirects = 5;
    protected $timeout      = 15;

    /**
     * @param int $maxRedirects Maximum redirects for client
     * @return ClientInterface
     */
    public function setMaxRedirects($redirects)
    {
        $this->maxRedirects = $redirects;
        return $this;
    }

    /**
     * @param  int $timeout Request timeout time for client in seconds
     * @return ClientInterface
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * @param  array $headers
     * @return void
     */
    public function normalizeHeaders(&$headers)
    {
        // Normalize headers
        array_walk( $headers,
            function(&$val, &$key)
            {
                $key = ucfirst( strtolower($key) );
                $val = ucfirst( strtolower($key) ) . ': ' . $val;
            }
        );
    }
}
