<?php
namespace OAuth\Common\Service;

use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Exception\Exception;

/**
 * Abstract OAuth service, version-agnostic
 */
abstract class AbstractService implements ServiceInterface
{
    protected function determineRequestUriFromPath($path, UriInterface $baseApiUri = null)
    {
        if( $path instanceof UriInterface ) {
            $uri = $path;
        } elseif( 0 === strpos('http://', $path) || 0 === strpos('https://', $path)  ) {
            $uri = new Uri($path);
        } else {
            if( null === $baseApiUri ) {
                throw new Exception('An absolute URI must be passed to ServiceInterface::request as no baseApiUri is set.');
            }

            $uri = clone $baseApiUri;
            if( false !== strpos($path, '?') ) {
                $parts = explode('?', $uri, 2);
                $path = $parts[0];
                $query = $parts[1];
                $uri->setQuery($query);
            }

            if( $path[0] === '/' ) {
                $path = substr($path, 1);
            }

            $uri->setPath($uri->getPath() . $path);
        }

        return $uri;
    }
}
