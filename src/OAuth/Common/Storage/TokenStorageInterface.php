<?php
/**
 * @category   OAuth
 * @package    Common
 * @subpackage Storage
 * @author     David Desberg <david@daviddesberg.com>
 * @copyright  Copyright (c) 2012 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace OAuth\Common\Storage;
use OAuth\Common\Token\TokenInterface;

/**
 * All token storage providers must implement this interface.
 */
interface TokenStorageInterface
{
    /**
     * @param \OAuth\Common\Token\TokenInterface $token
     */
    public function storeAccessToken(TokenInterface $token);

    /**
     * @return \OAuth\Common\Token\TokenInterface
     */
    public function retrieveAccessToken();
}
