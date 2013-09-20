<?php

namespace OAuth\Common\Consumer;

/**
 * Credentials Interface, credentials should implement this.
 */
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
