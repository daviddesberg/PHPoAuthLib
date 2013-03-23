<?php
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

class GitHub extends AbstractService
{
    /**
     * Defined scopes, see http://developer.github.com/v3/oauth/ for definitions
     */
    const SCOPE_USER = 'user';
    const SCOPE_PUBLIC_REPO = 'public_repo';
    const SCOPE_REPO = 'repo';
    const SCOPE_DELETE_REPO = 'delete_repo';
    const SCOPE_GIST = 'gist';

    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if( null === $baseApiUri ) {
            $this->baseApiUri = new Uri('https://api.github.com/');
        }
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://github.com/login/oauth/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://github.com/login/oauth/access_token');
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode( $responseBody, true );

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();


        $token->setAccessToken( $data['access_token'] );
        // Github tokens evidently never expire...
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset( $data['access_token'] );
        $token->setExtraParams( $data );

        return $token;
    }

    /**
     * Used to configure response type -- we want JSON from github, default is query string format
     *
     * @return array
     */
    protected function getExtraOAuthHeaders()
    {
        return array('Accept' => 'application/json');
    }

    /**
     * Required for GitHub API calls.
     *
     * @return array
     */
    protected function getExtraApiHeaders()
    {
        return array('Accept' => 'application/vnd.github.beta+json');
    }

    /**
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }
}
