<?php
namespace OAuth\Unit\Common\Consumer;

use OAuth\Common\Consumer\Credentials;

class CredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This is silly.
     */
    public function testGetters()
    {
        $a = new Credentials('a', 'b', 'c');
        $this->assertSame('a', $a->getConsumerId() );
        $this->assertSame('b', $a->getConsumerSecret() );
        $this->assertSame('c', $a->getCallbackUrl() );
    }
}
