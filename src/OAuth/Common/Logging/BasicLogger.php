<?php
namespace OAuth\Common\Logging;

/**
 * Defines methods common among all request loggers.
 */
class BasicLogger implements LoggingInterface
{
    protected static $requestCount = 0;
    protected static $requestLimit = 200;
    protected static $requests = array();

    /**
     * Log a request
     *
     * @param $path string|UriInterface
     * @param string $method HTTP method
     * @param array $body Request body if applicable (key/value pairs)
     * @param array $extraHeaders Extra headers if applicable. These will override service-specific any defaults.
     */
    public function logRequest($path, $method, $body, $extraHeaders, $request)
    {
        // Shift element off beginning of array if we're at the request limit
        if($this->requestCount() >= $this->requestLimit()) {
            array_shift($this->requests);
        }

        $this->requests[] = array(
            'path' => $path,
            'method' => $method,
            'body' => $body,
            'headers' => $extraHeaders,
            'request' => $request
        );

        $this->requestCount++;
    }

    /**
     * Get full request log
     *
     * @return array Results that have been executed and all data that has been passed with them
     */
    public function requests()
    {
        return $this->requests;
    }

    /**
     * Get last request run from log
     *
     * @return array The last request that has been executed and all data that has been passed with it
     */
    public function lastRequest()
    {
        return end($this->requests);
    }

    /**
     * Get a count of how many requests have been made
     *
     * @return int Total number of requests that have been made
     */
    public function requestCount()
    {
        return $this->requestCount;
    }

    /**
     * Get/set requests limit
     * A limit should be set by default to prevent request log from consuming and exhausing available memory
     *
     * @return int Request limit
     */
    public function requestLimit($limit = null)
    {
        if(null !== $limit) {
            $this->requestLimit = $limit;
        }
        return $this->requestLimit;
    }
}