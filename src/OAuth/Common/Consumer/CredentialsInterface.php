<?php
namespace OAuth\Common\Consumer;


interface CredentialsInterface
{
   
    public function __construct($consumerId, $consumerSecret, $callbackUrl);
    
    /**
     * @return string
     */
    public function getCallbackUrl();

    /**
     * @return string
     */
    public function getConsumerId();

    /**
     * @return string
     */
    public function getConsumerSecret();


}