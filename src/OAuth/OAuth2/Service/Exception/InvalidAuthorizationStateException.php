<?php

namespace OAuth\OAuth2\Service\Exception;

use Exception;

/**
 * Exception thrown when the state parameter received during the authorization process is invalid.
 */
class InvalidAuthorizationStateException extends Exception
{
}
