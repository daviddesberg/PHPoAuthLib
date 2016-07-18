<?php

namespace OAuth\OAuth1\Service;

use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;

class Flickr extends AbstractService
{
    protected $format;

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);
        if ($baseApiUri === null) {
            $this->baseApiUri = new Uri('https://api.flickr.com/services/rest/');
        }
    }

    public function getRequestTokenEndpoint()
    {
        return new Uri('https://www.flickr.com/services/oauth/request_token');
    }

    public function getAuthorizationEndpoint()
    {
        return new Uri('https://www.flickr.com/services/oauth/authorize');
    }

    public function getAccessTokenEndpoint()
    {
        return new Uri('https://www.flickr.com/services/oauth/access_token');
    }

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $uri = $this->determineRequestUriFromPath('/', $this->baseApiUri);
        $uri->addToQuery('method', $path);

        if (!empty($this->format)) {
            $uri->addToQuery('format', $this->format);

            if ($this->format === 'json') {
                $uri->addToQuery('nojsoncallback', 1);
            }
        }

        $token = $this->storage->retrieveAccessToken($this->service());
        $extraHeaders = array_merge($this->getExtraApiHeaders(), $extraHeaders);
        $authorizationHeader = array(
            'Authorization' => $this->buildAuthorizationHeaderForAPIRequest($method, $uri, $token, $body)
        );
        $headers = array_merge($authorizationHeader, $extraHeaders);

        return $this->httpClient->retrieveResponse($uri, $body, $headers, $method);
    }

    public function requestRest($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        return $this->request($path, $method, $body, $extraHeaders);
    }

    public function requestXmlrpc($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $this->format = 'xmlrpc';

        return $this->request($path, $method, $body, $extraHeaders);
    }

    public function requestSoap($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $this->format = 'soap';

        return $this->request($path, $method, $body, $extraHeaders);
    }

    public function requestJson($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $this->format = 'json';

        return $this->request($path, $method, $body, $extraHeaders);
    }

    public function requestPhp($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $this->format = 'php_serial';

        return $this->request($path, $method, $body, $extraHeaders);
    }
}
