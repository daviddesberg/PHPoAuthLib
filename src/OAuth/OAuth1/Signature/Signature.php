<?php
namespace OAuth\OAuth1\Signature;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\OAuth1\Signature\Exception\UnsupportedHashAlgorithmException;

class Signature implements SignatureInterface
{
    /**
     * @var \OAuth\Common\Consumer\Credentials
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
     * @param \OAuth\Common\Consumer\Credentials $credentials
     */
    public function __construct(Credentials $credentials)
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
     * @param \OAuth\Common\Http\Uri\UriInterface $uri
     * @param array $params
     * @param string $method
     * @return string
     */
    public function getSignature(UriInterface $uri, array $params, $method = 'POST')
    {
        parse_str($uri->getQuery(), $queryStringData);

        foreach(array_merge($queryStringData, $params) as $key => $value) {
            $signatureData[rawurlencode($key)] = rawurlencode($value);
        }

        ksort($signatureData);

        // determine base uri
        $baseUri = $uri->getScheme() . '://' . $uri->getRawAuthority();

        if ('/' == $uri->getPath()) {
            $baseUri.= $uri->hasExplicitTrailingHostSlash() ? '/' : '';
        } else {
            $baseUri .= $uri->getPath();
        }

        $baseString = strtoupper($method) . '&';
        $baseString.= rawurlencode($baseUri) . '&';
        $baseString.= rawurlencode($this->buildSignatureDataString($signatureData));

        return base64_encode($this->hash($baseString));
    }


    /**
     * @param array $signatureData
     * @return string
     */
    protected function buildSignatureDataString(array $signatureData)
    {
        $signatureString = '';
        $delimiter = '';
        foreach($signatureData as $key => $value) {
            $signatureString .= $delimiter . $key . '=' . $value;

            $delimiter = '&';
        }

        return $signatureString;
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
     * @return string
     * @throws UnsupportedHashAlgorithmException
     */
    protected function hash($data)
    {
        switch(strtoupper($this->algorithm)) {
            case 'HMAC-SHA1':
                return hash_hmac('sha1', $data, $this->getSigningKey(), true);

            default:
                throw new UnsupportedHashAlgorithmException('Unsupported hashing algorithm (' . $this->algorithm . ') used.');
        }
    }
}
