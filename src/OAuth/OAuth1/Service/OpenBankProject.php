<?php
namespace OAuth\OAuth1\Service;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\AbstractService;
use OAuth\OAuth1\Signature\SignatureInterface;
use OAuth\OAuth1\Token\StdOAuth1Token;
/**
 * This is Oauth v1 connector for Open Bank Project 
 * @author Amir Duran <amir.duran@gmail.com>
 */
class OpenBankProject extends AbstractService {

    public function __construct(CredentialsInterface $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, SignatureInterface $signature, UriInterface $baseApiUri = null) {
        parent::__construct($credentials, $httpClient, $storage, $signature, $baseApiUri);
        if (null === $baseApiUri) {
            $this->baseApiUri = new Uri('https://apisandbox.openbankproject.com');
        }
    }

    /**
     * This function parses AccessToken response from server. 
     * @link https://github.com/OpenBankProject/OBP-API/wiki/OAuth-1.0-Server Description of expected parameters
     * @param type $responseBody
     * @return \OAuth\OAuth1\Service\StdOAuth1Token
     * @throws TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody) {
        parse_str($responseBody, $data); //Parse received data from server
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data['error'])) {//If there is error in server response, print it
            throw new TokenResponseException(
            'Error in retrieving token: "' . $data['error'] . '"'
            );
        }
        $token = new StdOAuth1Token();
        $token->setRequestToken($data['oauth_token']);
        $token->setRequestTokenSecret($data['oauth_token_secret']);
        $token->setAccessToken($data['oauth_token']);
        $token->setAccessTokenSecret($data['oauth_token_secret']);
        $token->setEndOfLife(StdOAuth1Token::EOL_NEVER_EXPIRES);
        unset($data['oauth_token'], $data['oauth_token_secret']);
        $token->setExtraParams($data);
        return $token;
    }

    /**
     * This function parses RequestToken response.
     * @link https://github.com/OpenBankProject/OBP-API/wiki/OAuth-1.0-Server Description of expected parameters
     * @param type $responseBody
     * @return type
     * @throws TokenResponseException
     */
    protected function parseRequestTokenResponse($responseBody) {
        parse_str($responseBody, $data);//Parse data from server
        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse data received from server');
        } elseif (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] !== 'true') {
            throw new TokenResponseException('Your callback URL is not confirmed by server');
        }
        return $this->parseAccessTokenResponse($responseBody);
    }

    /**
     * This function returns AccessToken endpoint URL
     * @return string
     */
    public function getAccessTokenEndpoint() {
        return new Uri('https://apisandbox.openbankproject.com/oauth/token');
    }
    /**
     * This function returns Authorisation endpoint URL
     * @return string
     */
    public function getAuthorizationEndpoint() {
        return new Uri('https://apisandbox.openbankproject.com/oauth/authorize');
    }
    /**
     * This function returns RequestToken endpoint URL
     * @return string
     */
    public function getRequestTokenEndpoint() {
        return new Uri('https://apisandbox.openbankproject.com/oauth/initiate');       
    }
    
    /**
     * 
     * @param type $method
     * @param UriInterface $uri
     * @param \OAuth\OAuth1\Token\TokenInterface $token
     * @param type $bodyParams
     * @return string
     */
    protected function buildAuthorizationHeaderForAPIRequest($method, UriInterface $uri, \OAuth\OAuth1\Token\TokenInterface $token, $bodyParams = null) {
        $this->signature->setTokenSecret($token->getAccessTokenSecret());
        $authParameters = $this->getBasicAuthorizationHeaderInfo();
        if (isset($authParameters['oauth_callback'])) {
            unset($authParameters['oauth_callback']);
        }
        $authParameters = array_merge($authParameters, array('oauth_token' => $token->getAccessToken()));
        $authParameters = (is_array($bodyParams)) ? array_merge($authParameters, $bodyParams) : $authParameters;//In base class here is because $bodyParams array is never merged with $authParameters
        $authParameters['oauth_signature'] = $this->signature->getSignature($uri, $authParameters, $method);
        if (isset($bodyParams['oauth_session_handle'])) {
            $authParameters['oauth_session_handle'] = $bodyParams['oauth_session_handle'];
            unset($bodyParams['oauth_session_handle']);
        }

        $authorizationHeader = 'OAuth ';
        $delimiter = '';
        foreach ($authParameters as $key => $value) {
            $authorizationHeader .= $delimiter . rawurlencode($key) . '="' . rawurlencode($value) . '"';
            $delimiter = ', ';
        }
        return $authorizationHeader;
    }
}
