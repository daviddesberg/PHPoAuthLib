<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */

namespace OAuth\Common\Service;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\TokenStorageInterface;
use Artax\Http\Response;

interface ServiceInterface
{
    /**
     * @param \OAuth\Common\Consumer\Credentials $credentials
     * @param \OAuth\Common\Storage\TokenStorageInterface $storage
     * @param array $scopes array of scope values
     * @abstract
     */
    public function __construct(Credentials $credentials, TokenStorageInterface $storage, $scopes = []);

    /**
     * Retrieves and stores the OAuth2 access token after a successful authorization.
     *
     * @param string $code The access code from the callback.
     * @abstract
     */
    public function requestAccessToken($code);

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @abstract
     * @param array $additionalParameters
     * @return string
     */
    public function getAuthorizationUrl( array $additionalParameters = [] );

    /**
     * @abstract
     * @return string
     */
    public function getAuthorizationEndpoint();

    /**
     * @abstract
     * @return string
     */
    public function getAccessTokenEndpoint();

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @abstract
     * @return \OAuth\Common\Token\TokenInterface
     * @param \Artax\Http\Response $response
     */
    function parseAccessTokenResponse(Response $response);
}
