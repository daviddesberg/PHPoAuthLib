<?php
namespace OAuth\OAuth2\Service;

use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;

/**
 * Linkedin service.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 * @link http://developer.linkedin.com/documents/authentication
 */
class Linkedin extends AbstractService
{
    /**
     * Defined scopes
     * @link http://developer.linkedin.com/documents/authentication#granting
     */
    const SCOPE_R_BASICPROFILE = 'r_basicprofile';
    const SCOPE_R_FULLPROFILE  = 'r_fullprofile';
    const SCOPE_R_EMAILADDRESS = 'r_emailaddress';
    const SCOPE_R_NETWORK      = 'r_network';
    const SCOPE_R_CONTACTINFO  = 'r_contactinfo';
    const SCOPE_RW_NUS         = 'rw_nus';
    const SCOPE_RW_GROUPS      = 'rw_groups';
    const SCOPE_W_MESSAGES     = 'w_messages';

    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if( null === $baseApiUri ) {
            $this->baseApiUri = new Uri('https://api.linkedin.com/v1/');
        }
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://www.linkedin.com/uas/oauth2/authorization');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://www.linkedin.com/uas/oauth2/accessToken');
    }

    /**
     * Returns a class constant from ServiceInterface defining the authorization method used for the API
     * Header is the sane default.
     *
     * @return int
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING_V2;
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
        $token->setLifeTime( $data['expires_in'] );

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );
        unset( $data['expires_in'] );
        $token->setExtraParams( $data );

        return $token;
    }
}
