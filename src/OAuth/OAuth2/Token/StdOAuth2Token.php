<?php
/**
 * @category   OAuth
 * @package    OAuth2
 * @subpackage Token
 * @author     David Desberg <david@thedesbergs.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\OAuth2\Token;
use OAuth\Common\Token\AbstractToken;

/**
 * Standard OAuth2 token implementation.
 * Implements OAuth\OAuth2\Token\TokenInterface for any functionality that might not be provided by AbstractToken.
 */
class StdOAuth2Token extends AbstractToken implements TokenInterface
{
}
