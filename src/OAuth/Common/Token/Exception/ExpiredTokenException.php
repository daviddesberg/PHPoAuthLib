<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Token
 * @author     David Desberg <david@thedesbergs.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Common\Token\Exception;
use OAuth\Common\Exception\Exception;

/**
 * Exception thrown when an expired token is attempted to be used.
 */
class ExpiredTokenException extends Exception
{

}
