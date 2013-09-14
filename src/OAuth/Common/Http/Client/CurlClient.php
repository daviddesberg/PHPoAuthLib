<?php

namespace OAuth\Common\Http\Client;

use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Client implementation for cURL
 */
class CurlClient extends AbstractClient
{
    /**
     * If true, explicitly sets cURL to use SSL version 3. Use this if cURL
     * compiles with GnuTLS SSL.
     *
     * @var bool
     */
    private $forceSSL3 = false;

    /**
     * @param bool $force
     *
     * @return CurlClient
     */
    public function setForceSSL3($force)
    {
        $this->forceSSL3 = $force;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function doRetrieveResponse(
        UriInterface $endpoint,
        $requestBody,
        array $extraHeaders = array(),
        $method = 'POST'
    ) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $endpoint->getAbsoluteUri());

        if ($method === 'POST' || $method === 'PUT') {
            if ($requestBody && is_array($requestBody)) {
                $requestBody = http_build_query($requestBody, null, '&');
            }

            if ($method === 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($this->maxRedirects > 0) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $this->maxRedirects);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);

        if ($this->forceSSL3) {
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }

        $response     = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (false === $response) {
            $errNo  = curl_errno($ch);
            $errStr = curl_error($ch);
            curl_close($ch);
            if (empty($errStr)) {
                throw new TokenResponseException('Failed to request resource.', $responseCode);
            }
            throw new TokenResponseException('cURL Error # '.$errNo.': '.$errStr, $responseCode);
        }

        curl_close($ch);

        return $response;
    }
}
