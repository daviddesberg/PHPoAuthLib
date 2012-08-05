<?php
/**
 * @author Lusitanian <alusitanian@gmail.com>
 * Released under the MIT license.
 */
namespace OAuth\Common\Http;
use OAuth\Common\Http\Exception\TokenResponseException;
// require_once __DIR__. '/../../../../vendor/guzzle.phar'; -- if you want to use the phar, uncomment this line
use Guzzle\Service\Client;

/**
 * Client interface for GuzzlePHP.org
 */
class GuzzleClient implements ClientInterface
{
    /**
     * Any implementing HTTP providers should send a POST request to the provided endpoint with the parameters.
     * They should return, in string form, the response body and throw an exception on error.
     *
     * @param UriInterface $endpoint
     * @param array $params
     * @param array $extraHeaders
     * @param string $method
     * @throws TokenResponseException
     * @throws \InvalidArgumentException
     * @return string
     */
    public function retrieveResponse(UriInterface $endpoint, array $params, array $extraHeaders = [], $method = 'POST')
    {
        $method = strtoupper($method);

        if( $method === 'GET' && !empty($params) ) {
            throw new \InvalidArgumentException('No body parameters expected for "GET" request.');
        }

        // Normalize headers
        array_walk( $extraHeaders,
            function(&$val, &$key)
            {
                $key = ucfirst( strtolower($key) );
            }
        );

        foreach($extraHeaders as $name => $val)
        {
            $headerArray[] = "$name: $val";
        }


        $client = new Client();
        try {
            $response = $client->createRequest( $method, $endpoint->getAbsoluteUri(), $extraHeaders, $params )->send();

            if( $response->getStatusCode() >= 400 ) {
                throw new TokenResponseException('Server returned HTTP response code ' . $response->getStatusCode() );
            }
        } catch(\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            throw new TokenResponseException( 'Guzzle client error: ' . $e->getMessage() );
        }

        return $response->getBody(true);
    }

}