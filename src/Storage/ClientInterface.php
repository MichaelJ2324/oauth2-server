<?php
/**
 * OAuth 2.0 Client storage interface
 *
 */

namespace OAuth2\Server\Storage;

use OAuth2\Server\Entity\SessionEntity;

/**
 * Client storage interface
 */
interface ClientInterface extends StorageInterface
{
    /**
     * Validate a client
     *
     * @param string $clientId     The client's ID
     * @param string $clientSecret The client's secret (default = "null")
     * @param string $redirectUri  The client's redirect URI (default = "null")
     * @param string $grantType    The grant type used (default = "null")
     *
     * @return \OAuth2\Server\Entity\ClientEntity | null
     */
    public function get($clientId, $clientSecret = null, $redirectUri = null, $grantType = null);

    /**
     * Get the client associated with a session
     *
     * @param \OAuth2\Server\Entity\SessionEntity $session The session
     *
     * @return \OAuth2\Server\Entity\ClientEntity | null
     */
    public function getBySession(SessionEntity $session);
}
