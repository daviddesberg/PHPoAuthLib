<?php

namespace OAuth\OAuth1\Token;

use OAuth\OAuth1\Token\StdOAuth1Token;
use OAuth\Common\Http\Exception\TokenResponseException;

class StdOauth1AccessTokenResponseParser
{
    public function parse($responseBody)
    {
        $this->validateResponse($responseBody);

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

    private function validateResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }
        else if (null === $data
            || ! is_array($data)
            || ! isset($data['oauth_token'])
            || ! isset($data['oauth_token_secret'])
        ) {
            throw new TokenResponseException('Unable to parse response.');
        }
    }

    public function validateRequestTokenResponse($responseBody)
    {
        parse_str($responseBody, $data);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (!isset($data['oauth_callback_confirmed']) || $data['oauth_callback_confirmed'] !== 'true') {
            throw new TokenResponseException('Error in retrieving token.');
        }
    }
}
