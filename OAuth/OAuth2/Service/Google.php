<?php
namespace OAuth\OAuth2\Service;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Exception\InvalidTokenResponseException;
use Artax\Http\Response;

/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */
class Google extends AbstractService
{
    /**
     * Defined scopes
     * @todo complete this list
     */
    const SCOPE_EMAIL = 'https://www.googleapis.com/auth/userinfo.email';
    CONST SCOPE_PROFILE = 'https://www.googleapis.com/auth/userinfo.profile';

    public function getAuthorizationEndpoint()
    {
        return 'https://accounts.google.com/o/oauth2/auth';
    }

    public function getAccessTokenEndpoint()
    {
        return 'https://accounts.google.com/o/oauth2/token';
    }

    protected function parseAccessTokenResponse(Response $response)
    {
        if( $response->getStatusCode() >= 400 ) {
            throw new InvalidTokenResponseException( $response->getStatusCode() . ': ' . $response->getStatusDescription() );
        }


        $data = json_decode( $response->getBody(), true );

        if( null === $data || !is_array($data) ) {
            throw new InvalidTokenResponseException('Unable to parse response.');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken( $data['access_token'] );
        $token->setLifetime( $data['expires_in'] );

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );
        unset( $data['expires_in'] );

        $token->setExtraParams( $data );

        return $token;
    }

    /**
     * Returns whether or not the passed scope value is valid.
     *
     * @param $scope
     * @return bool
     */
    public function isValidScope($scope)
    {
        // All scopes for google start with this URL
        return ( strpos($scope, 'https://www.googleapis.com/auth/') === 0 );
    }
}
