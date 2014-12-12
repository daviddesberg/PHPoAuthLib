<?php

namespace OAuthTest\Unit\Common;

class TestHelper
{
    /**
     * @param string $response
     * @return \PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    public static function createStringResponse($responseBody)
    {
        $streamable = null;
        if ($responseBody) {
            $streamable = \PHPUnit_Framework_MockObject_Generator::getMock('\\Psr\\Http\\Message\\StreamableInterface');
            $streamable->expects(new \PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
                ->method("__toString")
                ->will(new \PHPUnit_Framework_MockObject_Stub_Return($responseBody));
        }

        $response = \PHPUnit_Framework_MockObject_Generator::getMock("\\Ivory\\HttpAdapter\\Message\\ResponseInterface");
        $response->expects(new \PHPUnit_Framework_MockObject_Matcher_InvokedCount(1))
            ->method("getBody")
            ->will(new \PHPUnit_Framework_MockObject_Stub_Return($streamable));

        return $response;
    }
}
