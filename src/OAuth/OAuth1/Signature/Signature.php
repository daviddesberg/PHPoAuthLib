<?php

namespace OAuth\OAuth1\Signature;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\Exception\UnsupportedHashAlgorithmException;

class Signature implements SignatureInterface
{
    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var string
     */
    protected $algorithm;

    /**
     * @var string
     */
    protected $tokenSecret = null;

    /**
     * @param CredentialsInterface $credentials
     */
    public function __construct(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param string $algorithm
     */
    public function setHashingAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @param string $token
     */
    public function setTokenSecret($token)
    {
        $this->tokenSecret = $token;
    }

    /**
     * @param UriInterface $uri
     * @param array        $params
     * @param string       $method
     *
     * @return string
     */
    public function getSignature(UriInterface $uri, array $params, $method = 'POST')
    {
        parse_str($uri->getQuery(), $queryStringData);

        $params = array_merge($queryStringData, $params);
        uksort($params, 'strcmp');

        $signatureData = $this->prepareParameters($params);

        // determine base uri
        $baseUri = $uri->getScheme() . '://' . $uri->getRawAuthority();

        if ('/' === $uri->getPath()) {
            $baseUri .= $uri->hasExplicitTrailingHostSlash() ? '/' : '';
        } else {
            $baseUri .= $uri->getPath();
        }

        $baseString = strtoupper($method) . '&';
        $baseString .= rawurlencode($baseUri) . '&';
        $baseString .= rawurlencode($this->buildSignatureDataString($signatureData));

        return base64_encode($this->hash($baseString));
    }

    /**
     * @param array $signatureData
     *
     * @return string
     */
    protected function buildSignatureDataString(array $signatureData)
    {
        return http_build_query($signatureData, null, '&', PHP_QUERY_RFC3986);
    }

    /**
     * Convert booleans to strings, removed unset parameters, and sorts the array
     *
     * @param array $data Data array
     *
     * @return array
     */
    public function prepareParameters($data)
    {
        ksort($data);
        foreach ($data as $key => &$value) {
            switch (gettype($value)) {
                case 'NULL':
                    unset($data[$key]);
                    break;
                case 'array':
                    $data[$key] = self::prepareParameters($value);
                    break;
                case 'boolean':
                    $data[$key] = $value ? 'true' : 'false';
                    break;
            }
        }
        return $data;
    }

    /**
     * @return string
     */
    protected function getSigningKey()
    {
        $signingKey = rawurlencode($this->credentials->getConsumerSecret()) . '&';
        if ($this->tokenSecret !== null) {
            $signingKey .= rawurlencode($this->tokenSecret);
        }

        return $signingKey;
    }

    /**
     * @param string $data
     *
     * @return string
     *
     * @throws UnsupportedHashAlgorithmException
     */
    protected function hash($data)
    {
        switch (strtoupper($this->algorithm)) {
            case 'HMAC-SHA1':
                return hash_hmac('sha1', $data, $this->getSigningKey(), true);
            default:
                throw new UnsupportedHashAlgorithmException(
                    'Unsupported hashing algorithm (' . $this->algorithm . ') used.'
                );
        }
    }
}
