<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Service;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri;

class Google extends AbstractService
{
    /**
     * Defined scopes
     * @todo complete this list (place in array?)
     */
    const SCOPE_EMAIL = 'https://www.googleapis.com/auth/userinfo.email';
    CONST SCOPE_PROFILE = 'https://www.googleapis.com/auth/userinfo.profile';

    public function getAuthorizationEndpoint()
    {
        return new Uri('accounts.google.com', '/o/oauth2/auth', 'http', 443, true);
    }

    public function getAccessTokenEndpoint()
    {
        return new Uri('accounts.google.com', '/o/oauth2/token', 'http', 443, true);
    }

    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode( $responseBody, true );

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
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
