<?php
/**
 * 500px service.
 *
 * @author  Pedro Amorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    https://developers.500px.com/
 */

namespace OAuth\OAuth1\Service;

use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Client\ClientInterface;

/**
 * 500px service.
 *
 * @author  Pedro Amorim <contact@pamorim.fr>
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @link    https://developers.500px.com/
 */
class FiveHundredPx extends AbstractService
{
    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        UriInterface $baseApiUri = null
    ) {
        parent::__construct(
            $credentials,
            $httpClient,
            $storage,
            $signature,
            $baseApiUri
        );

        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://api.500px.com/v1/');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestTokenEndpoint()
    {
        return new Uri('https://api.500px.com/v1/oauth/request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://api.500px.com/v1/oauth/authorize');
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.500px.com/v1/oauth/access_token');
    }
    
}
