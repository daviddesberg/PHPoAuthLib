<?php

namespace OAuth\OAuth1\Token;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;

class StdOauth1TokenResponseParser
{
    /**
     * Parses the request token response and returns a TokenInterface.
     * This is only needed to verify the `oauth_callback_confirmed` parameter. The actual
     * parsing logic is contained in the access token parser.
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     *
     * @throws TokenResponseException
     */
    public function parseRequestTokenResponse($responseBody)
    {
        $this->validateRequestTokenResponse($responseBody);

        return $this->parseAccessTokenResponse($responseBody);
    }

    /**
     * Parses the access token response and returns a TokenInterface.
     *
     * @param string $responseBody
     *
     * @return TokenInterface
     *
     * @throws TokenResponseException
     */
    public function parseAccessTokenResponse($responseBody)
    {
        $this->validateAccessTokenResponse($responseBody);

        parse_str($responseBody, $data);
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
     * This verifies the `oauth_token` and `oauth_token_secret` parameters.
     *
     * @param string $responseBody
     *
     * @throws TokenResponseException
     */
    private function validateAccessTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        } elseif (null === $data || ! is_array($data)
            || ! isset($data['oauth_token'])
            || ! isset($data['oauth_token_secret'])
        ) {
            throw new TokenResponseException('Unable to parse response.');
        }
    }

    /**
     * This verifies the `oauth_callback_confirmed` parameter.
     *
     * @param string $responseBody
     *
     * @throws TokenResponseException
     */
    private function validateRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (! isset($data['oauth_callback_confirmed'])
            || $data['oauth_callback_confirmed'] !== 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }
    }
}
