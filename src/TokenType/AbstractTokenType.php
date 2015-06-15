<?php
/**
 * OAuth 2.0 Abstract Token Type
 *
 */

namespace OAuth2\Server\TokenType;

use OAuth2\Server\AbstractServer;
use OAuth2\Server\Entity\SessionEntity;

abstract class AbstractTokenType
{
    /**
     * Response array
     *
     * @var array
     */
    protected $response = [];

    /**
     * Server
     *
     * @var \OAuth2\Server\AbstractServer $server
     */
    protected $server;

    /**
     * Server
     *
     * @var \OAuth2\Server\Entity\SessionEntity $session
     */
    protected $session;

    /**
     * {@inheritdoc}
     */
    public function setServer(AbstractServer $server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSession(SessionEntity $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParam($key, $value)
    {
        $this->response[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getParam($key)
    {
        return isset($this->response[$key]) ? $this->response[$key] : null;
    }
}
