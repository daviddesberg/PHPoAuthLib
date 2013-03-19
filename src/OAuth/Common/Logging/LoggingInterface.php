<?php
namespace OAuth\Common\Logging;

/**
 * Defines methods common among all request loggers.
 */
interface LoggingInterface
{
    /**
     * Log a request
     *
     * @abstract
     * @param $path string|UriInterface
     * @param string $method HTTP method
     * @param array $body Request body if applicable (key/value pairs)
     * @param array $extraHeaders Extra headers if applicable. These will override service-specific any defaults.
     */
    public function logRequest($path, $method, $body, $extraHeaders, $result);
}