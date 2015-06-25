<?php
/**
 * OAuth 2.0 Token Type Interface
 *
 */

namespace OAuth2\Server\TokenType;

use OAuth2\Server\AbstractServer;
use OAuth2\Server\Entity\SessionEntity;
use OAuth2\Server\Request\HandlerInterface as RequestHandler;

interface TokenTypeInterface
{
    /**
     * Generate a response
     *
     * @return array
     */
    public function generateResponse();

    /**
     * Set the server
     *
     * @param \OAuth2\Server\AbstractServer $server
     *
     * @return self
     */
    public function setServer(AbstractServer $server);

    /**
     * Set a key/value response pair
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value);

    /**
     * Get a key from the response array
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParam($key);

    /**
     * @param \OAuth2\Server\Entity\SessionEntity $session
     *
     * @return self
     */
    public function setSession(SessionEntity $session);

    /**
     * Determine the access token in the authorization header
     *
     * @param \OAuth2\Server\Request\HandlerInterface
     * @param String
	 *
     * @return string
     */
    public function determineAccessTokenInHeader(RequestHandler $requestHandler, $authHeader = 'Authorization');
}
