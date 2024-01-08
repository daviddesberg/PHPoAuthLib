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
    protected $tokenSecret;

    public function __construct(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param string $algorithm
     */
    public function setHashingAlgorithm($algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    /**
     * @param string $token
     */
    public function setTokenSecret($token): void
    {
        $this->tokenSecret = $token;
    }

    /**
     * @param string       $method
     *
     * @return string
     */
    public function getSignature(UriInterface $uri, array $params, $method = 'POST')
    {
        parse_str($uri->getQuery(), $queryStringData);

        $signatureData = array_merge($queryStringData, $params);
        $this->ksortRecursive($signatureData);

        // determine base uri
        $baseUri = $uri->getScheme() . '://' . $uri->getRawAuthority();

        if ('/' === $uri->getPath()) {
            $baseUri .= $uri->hasExplicitTrailingHostSlash() ? '/' : '';
        } else {
            $baseUri .= $uri->getPath();
        }

        $baseString = strtoupper($method) . '&';
        $baseString .= rawurlencode($baseUri) . '&';
        // The url paramaters are first encoded induvidually by http_build_query, then the result is encoded again.
        $baseString .= rawurlencode(http_build_query($signatureData, '', '&', PHP_QUERY_RFC3986));

        return base64_encode($this->hash($baseString));
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

    /**
     * Rescursively sorts an array by key.
     * @param string $data
     *
     * @return string
     */
    protected function ksortRecursive(&$array, $sort_flags = SORT_REGULAR) {
        if (!is_array($array)) {
            return false;
        }
        ksort($array, $sort_flags);
        foreach ($array as &$arr) {
            $this->ksortRecursive($arr, $sort_flags);
        }
        return true;
    }
}
