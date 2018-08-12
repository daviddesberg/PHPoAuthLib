<?php

namespace OAuth\Common\Http\Exception;

use OAuth\Common\Exception\Exception;

/**
 * Exception relating to token response from service.
 */
class TokenResponseException extends Exception
{
    /**
     * The raw response body. This may be crucial to handling to the response.
     * However, it may also contain sensitive information that you would not
     * want to bubble up to the user, so it will never be returned in the error
     * message itself.
     *
     * @var string
     */
    protected $body = '';

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
