<?php


namespace Unit\OAuth2\Service;
use Dotenv\Dotenv;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Storage\Session;
use OAuth\OAuth2\Service\MicrosoftGraph;
use OAuth\OAuth2\Token\TokenInterface;

/**
 * Class MicrosoftGraph
 * @package Unit\OAuth2\Service
 * @author Juan Diaz - FuriosoJack <iam@furiosojack.com>
 */
class MicrosoftGraphTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test para correcta construccion con interfaces
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new MicrosoftGraph(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * Test para correcta construccion de la instancia como servicio abstracto
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new MicrosoftGraph(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * Test para correcta construccion de la instancia con scopes y uriApi interface
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new MicrosoftGraph(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            array(),
            $this->getMock('\\OAuth\\Common\\Http\\Uri\\UriInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * Test de obtenecion de url de autorizacion
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new MicrosoftGraph(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://login.microsoftonline.com/common/oauth2/authorize', $service->getAuthorizationEndpoint()->getAbsoluteUri());
    }

    /**
     * Test para obtencion de url de endpoint del token
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new MicrosoftGraph(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Http\\Client\\ClientInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://login.microsoftonline.com/common/oauth2/token', $service->getAccessTokenEndpoint()->getAbsoluteUri());
    }


}