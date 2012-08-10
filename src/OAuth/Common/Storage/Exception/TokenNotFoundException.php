<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Storage
 * @author     David Desberg <david@thedesbergs.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\Common\Storage\Exception;
use OAuth\Common\Exception\Exception;

/**
 * Exception thrown when a token is not found in storage.
 */
class TokenNotFoundException extends StorageException
{

}
