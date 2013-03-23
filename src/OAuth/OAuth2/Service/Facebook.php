<?php
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class Facebook extends AbstractService
{
    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if( null === $baseApiUri ) {
            $this->baseApiUri = new Uri('https://graph.facebook.com/');
        }
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://www.facebook.com/dialog/oauth');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://graph.facebook.com/oauth/access_token');
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        // Facebook gives us a query string ... Oh wait. JSON is too simple, understand ?
        parse_str($responseBody, $data);

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();

        $token->setAccessToken( $data['access_token'] );
        $token->setLifeTime( $data['expires'] );

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );
        unset( $data['expires'] );
        $token->setExtraParams( $data );

        return $token;
    }
}
