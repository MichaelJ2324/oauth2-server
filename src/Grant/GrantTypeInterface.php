<?php
/**
 * OAuth 2.0 Grant type interface
 *
 */

namespace OAuth2\Server\Grant;

use OAuth2\Server\AuthorizationServer;
use OAuth2\Server\Entity\ClientEntity;

/**
 * Grant type interface
 */
interface GrantTypeInterface
{
    /**
     * Return the identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Return the identifier
     *
     * @param string $identifier
     *
     * @return self
     */
    public function setIdentifier($identifier);

    /**
     * Return the response type
     *
     * @return string
     */
    public function getResponseType();

    /**
     * Inject the authorization server into the grant
     *
     * @param \OAuth2\Server\AuthorizationServer $server The authorization server instance
     *
     * @return self
     */
    public function setAuthorizationServer(AuthorizationServer $server);

    /**
     * Complete the grant flow
     *
	 * @param \OAuth2\Server\Entity\ClientEntity $client The validated Client Entity
	 *
     * @return array
     */
    public function completeFlow(ClientEntity $client);
}
