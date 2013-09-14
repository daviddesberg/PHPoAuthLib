<?php

namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Client implementation for streams/file_get_contents
 */
class StreamClient extends AbstractClient
{
    /**
     * {@inheritdoc}
     */
    protected function doRetrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST',
        $multipart = false
    ) {
        if ($multipart) {
            throw new \RuntimeException(sprintf('Stream adapter does not supports files uploading.'));
        }

        $host = 'Host: '.$endpoint->getHost();
        // Append port to Host if it has been specified
        if ($endpoint->hasExplicitPortSpecified()) {
            $host .= ':'.$endpoint->getPort();
        }

        if (is_array($requestBody)) {
            $requestBody = http_build_query($requestBody, '', '&');
        }
        $extraHeaders['Content-length'] = 'Content-length: '.strlen($requestBody);

        $context = $this->generateStreamContext($requestBody, $extraHeaders, $method);

        $level = error_reporting(0);
        $response = file_get_contents($endpoint->getAbsoluteUri(), false, $context);
        error_reporting($level);
        if (false === $response) {
            $lastError = error_get_last();
            if (is_null($lastError)) {
                throw new TokenResponseException('Failed to request resource.');
            }
            throw new TokenResponseException($lastError['message']);
        }

        return $response;
    }

    private function generateStreamContext($body, $headers, $method)
    {
        return stream_context_create(
            array(
                'http' => array(
                    'method'           => $method,
                    'header'           => implode("\r\n", array_values($headers)),
                    'content'          => $body,
                    'protocol_version' => '1.1',
                    'user_agent'       => $this->userAgent,
                    'max_redirects'    => $this->maxRedirects,
                    'timeout'          => $this->timeout
                ),
            )
        );
    }
}
