<?php
/**
 * OAuth 2.0 abstract storage
 *
 */

namespace OAuth2\Server\Storage;

use OAuth2\Server\AbstractServer;

/**
 * Abstract storage class
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Server
     *
     * @var \OAuth2\Server\AbstractServer $server
     */
    protected $server;

    /**
     * Set the server
     *
     * @param \OAuth2\Server\AbstractServer $server
     *
     * @return self
     */
    public function setServer(AbstractServer $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Return the server
     *
     * @return \OAuth2\Server\AbstractServer
     */
    protected function getServer()
    {
        return $this->server;
    }
}
