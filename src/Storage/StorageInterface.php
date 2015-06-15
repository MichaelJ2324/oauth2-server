<?php
/**
 * OAuth 2.0 Storage interface
 *
 */

namespace OAuth2\Server\Storage;

use OAuth2\Server\AbstractServer;

/**
 * Storage interface
 */
interface StorageInterface
{
    /**
     * Set the server
     *
     * @param \OAuth2\Server\AbstractServer $server
     */
    public function setServer(AbstractServer $server);
}
