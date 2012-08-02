<?php
namespace OAuth2\Service;

/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */
interface ServiceInterface
{
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
}
