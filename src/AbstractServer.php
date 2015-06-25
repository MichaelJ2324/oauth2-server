<?php
/**
 * OAuth 2.0 Abstract Server
 *
 */

namespace OAuth2\Server;

use OAuth2\Server\Request\HandlerInterface as RequestHandler;
use OAuth2\Server\Storage\AccessTokenInterface;
use OAuth2\Server\Storage\AuthCodeInterface;
use OAuth2\Server\Storage\ClientInterface;
use OAuth2\Server\Storage\MacTokenInterface;
use OAuth2\Server\Storage\RefreshTokenInterface;
use OAuth2\Server\Storage\ScopeInterface;
use OAuth2\Server\Storage\SessionInterface;
use OAuth2\Server\TokenType\TokenTypeInterface;
use OAuth2\Server\TokenType\Bearer;

/**
 * OAuth 2.0 Resource Server
 */
abstract class AbstractServer
{
    /**
     * The request object
     *
     * @var \OAuth2\Server\Request\HandlerInterface
     */
    protected $requestHandler;

    /**
     * Session storage
     *
     * @var \OAuth2\Server\Storage\SessionInterface
     */
    protected $sessionStorage;

    /**
     * Access token storage
     *
     * @var \OAuth2\Server\Storage\AccessTokenInterface
     */
    protected $accessTokenStorage;

    /**
     * Refresh token storage
     *
     * @var \OAuth2\Server\Storage\RefreshTokenInterface
     */
    protected $refreshTokenStorage;

    /**
     * Auth code storage
     *
     * @var \OAuth2\Server\Storage\AuthCodeInterface
     */
    protected $authCodeStorage;

    /**
     * Scope storage
     *
     * @var \OAuth2\Server\Storage\ScopeInterface
     */
    protected $scopeStorage;

    /**
     * Client storage
     *
     * @var \OAuth2\Server\Storage\ClientInterface
     */
    protected $clientStorage;

    /**
     * @var \OAuth2\Server\Storage\MacTokenInterface
     */
    protected $macStorage;

    /**
     * Token type
     *
     * @var \OAuth2\Server\TokenType\TokenTypeInterface
     */
    protected $tokenType;

    /**
     * Abstract server constructor
	 *
	 * @param \OAuth2\Server\Request\HandlerInterface
	 *
	 * @return self
     */
    public function __construct(RequestHandler $requestHandler)
    {
		// Set Bearer as the default token type
		$this->setTokenType(new Bearer());

		$this->setRequestHandler($requestHandler);

		return $this;
    }

    /**
     * Sets the Request Object
     *
     * @param \OAuth2\Server\Request\HandlerInterface The RequestHandler Object
     *
     * @return self
     */
    public function setRequestHandler(RequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;

        return $this;
    }
	/**
	 * Gets the Request Object
	 *
	 * @param \OAuth2\Server\Request\HandlerInterface The RequestHandler Object
	 *
	 * @return self
	 */
	public function getRequestHandler()
	{
		return $this->requestHandler;
	}

    /**
     * Set the client storage
     *
     * @param \OAuth2\Server\Storage\ClientInterface $storage
     *
     * @return self
     */
    public function setClientStorage(ClientInterface $storage)
    {
        $storage->setServer($this);
        $this->clientStorage = $storage;

        return $this;
    }

    /**
     * Set the session storage
     *
     * @param \OAuth2\Server\Storage\SessionInterface $storage
     *
     * @return self
     */
    public function setSessionStorage(SessionInterface $storage)
    {
        $storage->setServer($this);
        $this->sessionStorage = $storage;

        return $this;
    }

    /**
     * Set the access token storage
     *
     * @param \OAuth2\Server\Storage\AccessTokenInterface $storage
     *
     * @return self
     */
    public function setAccessTokenStorage(AccessTokenInterface $storage)
    {
        $storage->setServer($this);
        $this->accessTokenStorage = $storage;

        return $this;
    }

    /**
     * Set the refresh token storage
     *
     * @param \OAuth2\Server\Storage\RefreshTokenInterface $storage
     *
     * @return self
     */
    public function setRefreshTokenStorage(RefreshTokenInterface $storage)
    {
        $storage->setServer($this);
        $this->refreshTokenStorage = $storage;

        return $this;
    }

    /**
     * Set the auth code storage
     *
     * @param \OAuth2\Server\Storage\AuthCodeInterface $storage
     *
     * @return self
     */
    public function setAuthCodeStorage(AuthCodeInterface $storage)
    {
        $storage->setServer($this);
        $this->authCodeStorage = $storage;

        return $this;
    }

    /**
     * Set the scope storage
     *
     * @param \OAuth2\Server\Storage\ScopeInterface $storage
     *
     * @return self
     */
    public function setScopeStorage(ScopeInterface $storage)
    {
        $storage->setServer($this);
        $this->scopeStorage = $storage;

        return $this;
    }

    /**
     * Return the client storage
     *
     * @return \OAuth2\Server\Storage\ClientInterface
     */
    public function getClientStorage()
    {
        return $this->clientStorage;
    }

    /**
     * Return the scope storage
     *
     * @return \OAuth2\Server\Storage\ScopeInterface
     */
    public function getScopeStorage()
    {
        return $this->scopeStorage;
    }

    /**
     * Return the session storage
     *
     * @return \OAuth2\Server\Storage\SessionInterface
     */
    public function getSessionStorage()
    {
        return $this->sessionStorage;
    }

    /**
     * Return the refresh token storage
     *
     * @return \OAuth2\Server\Storage\RefreshTokenInterface
     */
    public function getRefreshTokenStorage()
    {
        return $this->refreshTokenStorage;
    }

    /**
     * Return the access token storage
     *
     * @return \OAuth2\Server\Storage\AccessTokenInterface
     */
    public function getAccessTokenStorage()
    {
        return $this->accessTokenStorage;
    }

    /**
     * Return the auth code storage
     *
     * @return \OAuth2\Server\Storage\AuthCodeInterface
     */
    public function getAuthCodeStorage()
    {
        return $this->authCodeStorage;
    }

    /**
     * Set the access token type
     *
     * @param TokenTypeInterface $tokenType The token type
     *
     * @return void
     */
    public function setTokenType(TokenTypeInterface $tokenType)
    {
        $tokenType->setServer($this);
        $this->tokenType = $tokenType;
    }

    /**
     * Get the access token type
     *
     * @return TokenTypeInterface
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @return MacTokenInterface
     */
    public function getMacStorage()
    {
        return $this->macStorage;
    }

    /**
     * @param MacTokenInterface $macStorage
     */
    public function setMacStorage(MacTokenInterface $macStorage)
    {
        $this->macStorage = $macStorage;
    }
}
