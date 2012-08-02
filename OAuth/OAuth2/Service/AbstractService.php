<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\OAuth2\Service;

use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Exception\InvalidTokenResponseException;
use OAuth\Common\Exception\InvalidScopeException;
use OAuth\Common\Exception\MissingRefreshTokenException;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Service\ServiceInterface;

use Artax\Http\Client;
use Artax\Http\Response;
use Artax\Http\StdRequest;

abstract class AbstractService implements ServiceInterface
{
    /**
     * @var \OAuth\Common\Consumer\Credentials
     */
    protected $credentials;

    /**
     * @var \OAuth\Common\Storage\TokenStorageInterface
     */
    protected $storage;

    /**
     * @var array
     */
    protected $scopes;

    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param array $scopes array of scope values
     * @throws InvalidScopeException
     */
    public function __construct(Credentials $credentials, TokenStorageInterface $storage, $scopes = [])
    {
        $this->credentials = $credentials;
        $this->storage = $storage;

        foreach($scopes as $scope)
        {
            if( !$this->isValidScope($scope) ) {
                throw new InvalidScopeException('Scope ' . $scope . ' is not valid for service ' . get_class($this) );
            }
        }

        $this->scopes = $scopes;

    }

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @param array $additionalParameters
     * @return string
     */
    public function getAuthorizationUrl( array $additionalParameters = [] )
    {
        $parameters = array_merge($additionalParameters,
            [
                'type' => 'web_server',
                'client_id' => $this->credentials->getKey(),
                'redirect_uri' => $this->credentials->getCallbackUrl(),
                'response_type' => 'code',
            ]
        );

        $parameters['scope'] = implode(' ', $this->scopes);

        // Build the url
        $url = $this->getAuthorizationEndpoint();
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($parameters);

        return $url;
    }


    /**
     * Retrieves and stores the OAuth2 access token after a successful authorization.
     *
     * @param string $code The access code from the callback.
     * @throws InvalidTokenResponseException
     */
    public function requestAccessToken($code)
    {
        $parameters =
        [
            'code' => $code,
            'client_id' => $this->credentials->getKey(),
            'client_secret' => $this->credentials->getSecret(),
            'redirect_uri' => $this->credentials->getCallbackUrl(),
            'grant_type' => 'authorization_code',

        ];

        // Yay three nested method calls
        $this->storage->storeAccessToken( $this->parseAccessTokenResponse( $this->sendTokenRequest($parameters) ) );
    }

    /**
     * Refreshes an OAuth2 access token.
     *
     * @param \OAuth\Common\Token\TokenInterface $token
     * @throws \OAuth\Common\Exception\MissingRefreshTokenException
     */
    public function refreshAccessToken(TokenInterface $token)
    {
        $refreshToken = $token->getRefreshToken();

        if ( empty( $refreshToken ) ) {
            throw new MissingRefreshTokenException();
        }

        $parameters =
        [
            'grant_type' => 'refresh_token',
            'type' => 'web_server',
            'client_id' => $this->credentials->getKey(),
            'client_secret' => $this->credentials->getSecret(),
            'refresh_token' => $refreshToken,
        ];

        $this->storage->storeAccessToken( $this->parseAccessTokenResponse( $this->sendTokenRequest($parameters) )  );
    }

    /**
     * Sends a request to the token endpoint.
     *
     * @param $parameters
     * @return \Artax\Http\Response
     */
    protected function sendTokenRequest(array $parameters)
    {
        /*
        $parameters['scope'] = implode(' ', $this->scopes);
        $parameters['response_type'] = '';
        */

        // Build and send the HTTP request
        $request = new StdRequest( $this->getAccessTokenEndpoint(), 'POST', [], http_build_query($parameters), [ 'Content-type' => 'application/x-www-form-urlencoded', 'Host' => parse_url($this->getAccessTokenEndpoint(), PHP_URL_HOST)] );
        $client = new Client();

        // Retrieve the response
        return $client->request($request);
    }

    /**
     * Return whether or not the passed scope value is valid.
     *
     * @param $scope
     * @return bool
     */
    public function isValidScope($scope)
    {
        return true;
    }

}
