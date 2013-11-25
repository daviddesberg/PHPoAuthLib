<?php

namespace OAuth\Common\Http\Exception;

use OAuth\Common\Exception\Exception;

class GuzzleException extends Exception
{
    protected $response;

    public function getGuzzleResponse()
    {
        return $this->response;
    }

    public function setGuzzleResponse($response)
    {
        $this->response = $response;
    }
}
