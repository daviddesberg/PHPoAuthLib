<?php

namespace OAuth\OAuth1\Service;

use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;

class Etsy extends AbstractService
{

    protected $scopes = array();

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        UriInterface $baseApiUri = null
    ) {
        parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://openapi.etsy.com/v2/');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTokenEndpoint()
    {
        $uri = new Uri($this->baseApiUri . 'oauth/request_token');
        $scopes = $this->getScopes();

        if (count($scopes)) {
            $uri->setQuery('scope=' . implode('%20', $scopes));
        }

        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri($this->baseApiUri);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri($this->baseApiUri . 'oauth/access_token');
    }
    
    /**
     * Set the scopes for permissions
     * @see https://www.etsy.com/developers/documentation/getting_started/oauth#section_permission_scopes
     * @param array $scopes
     *
     * @return $this
     */
    public function setScopes(array $scopes)
    {
        if (!is_array($scopes)) {
            $scopes = array();
        }

        $this->scopes = $scopes;
        return $this;
    }

    /**
     * Return the defined scopes
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }
}
