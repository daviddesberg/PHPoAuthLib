<?php

namespace OAuth\Common\Http\Client;

/**
 * Response object.
 */
class Response
{
    protected $content;
    protected $status;
    protected $headers;

    /**
     * Constructor.
     *
     * @param string  $content The content of the response
     * @param integer $status  The response status code
     * @param array   $headers An array of headers
     */
    public function __construct($content = '', $status = 200, array $headers = array())
    {
        $this->content = $content;
        $this->status  = $status;
        $this->headers = $headers;
    }

    /**
     * Converts the response object to string containing all headers and the response content.
     *
     * @return string The response with headers and content
     */
    public function __toString()
    {
        return $this->content;
    }

    /**
     * Gets the response content.
     *
     * @return string The response content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gets the response status code.
     *
     * @return integer The response status code
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Gets the response headers.
     *
     * @return array The response headers
     *
     * @api
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
