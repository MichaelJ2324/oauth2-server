<?php
/**
 * OAuth 2.0 Client entity
 *
 */

namespace OAuth2\Server\Entity;

use OAuth2\Server\AbstractServer;

/**
 * Client entity class
 */
class ClientEntity
{
    use EntityTrait;

    /**
     * Client identifier
     *
     * @var string
     */
    protected $id = null;

    /**
     * Client secret
     *
     * @var string
     */
    protected $secret = null;

    /**
     * Client name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Client redirect URI
     *
     * @var string
     */
    protected $redirectUri = null;

    /**
     * Authorization or resource server
     *
     * @var \OAuth2\Server\AbstractServer
     */
    protected $server;

    /**
     * __construct
     *
     * @param \OAuth2\Server\AbstractServer $server
     *
     * @return self
     */
    public function __construct(AbstractServer $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Return the client identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the client secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Get the client name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returnt the client redirect URI
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }
}
