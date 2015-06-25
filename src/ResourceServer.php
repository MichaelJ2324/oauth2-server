<?php
/**
 * OAuth 2.0 Resource Server
 *
 */

namespace OAuth2\Server;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Exception\AccessDeniedException;
use OAuth2\Server\Exception\InvalidRequestException;
use OAuth2\Server\Storage\AccessTokenInterface;
use OAuth2\Server\Storage\ClientInterface;
use OAuth2\Server\Storage\ScopeInterface;
use OAuth2\Server\Storage\SessionInterface;
use OAuth2\Server\TokenType\Bearer;
use OAuth2\Server\Request\HandlerInterface;

/**
 * OAuth 2.0 Resource Server
 */
class ResourceServer extends AbstractServer
{
    /**
     * The access token
     *
     * @var \OAuth2\Server\Entity\AccessTokenEntity
     */
    protected $accessToken;

    /**
     * The query string key which is used by clients to present the access token (default: access_token)
     *
     * @var string
     */
    protected $tokenKey = 'access_token';

	/**
	 * The Header key which is used by clients to present the access token (default: Authorization)
	 *
	 * @var string
	 */
	protected $authHeader = 'Authorization';

    /**
     * Initialise the resource server
     *
	 * @param \OAuth2\Server\Request\HandlerInterface     $requestHandler
     * @param \OAuth2\Server\Storage\SessionInterface     $sessionStorage
     * @param \OAuth2\Server\Storage\AccessTokenInterface $accessTokenStorage
     * @param \OAuth2\Server\Storage\ClientInterface      $clientStorage
     * @param \OAuth2\Server\Storage\ScopeInterface       $scopeStorage
     *
     * @return self
     */
    public function __construct(
		HandlerInterface $requestHandler,
        SessionInterface $sessionStorage,
        AccessTokenInterface $accessTokenStorage,
        ClientInterface $clientStorage,
        ScopeInterface $scopeStorage
    ) {
        $this->setSessionStorage($sessionStorage);
        $this->setAccessTokenStorage($accessTokenStorage);
        $this->setClientStorage($clientStorage);
        $this->setScopeStorage($scopeStorage);

        parent::__construct($requestHandler);

        return $this;
    }

    /**
     * Sets the query string key for the access token.
     *
     * @param string $key The new query string key
     *
     * @return self
     */
    public function setIdKey($key)
    {
        $this->tokenKey = $key;

        return $this;
    }
	/**
	 * Sets the Auth Header key for which access token will be passed.
	 *
	 * @param string $authHeader The new authHeader string key
	 *
	 * @return self
	 */
	public function setAuthHeader($authHeader)
	{
		$this->authHeader = $authHeader;

		return $this;
	}

    /**
     * Gets the access token
     *
     * @return \OAuth2\Server\Entity\AccessTokenEntity
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Checks if the access token is valid or not
     *
     * @param bool                                                $headerOnly Limit Access Token to Authorization header
     * @param \OAuth2\Server\Entity\AccessTokenEntity|null $accessToken Access Token
     *
     * @throws \OAuth2\Server\Exception\AccessDeniedException
     * @throws \OAuth2\Server\Exception\InvalidRequestException
     *
     * @return bool
     */
    public function isValidRequest($headerOnly = true, $accessToken = null)
    {
        $accessTokenString = ($accessToken !== null)
                                ? $accessToken
                                : $this->determineAccessToken($headerOnly);

        // Set the access token
        $this->accessToken = $this->getAccessTokenStorage()->get($accessTokenString);

        // Ensure the access token exists
        if (!$this->accessToken instanceof AccessTokenEntity) {
            throw new AccessDeniedException();
        }

        // Check the access token hasn't expired
        // Ensure the auth code hasn't expired
        if ($this->accessToken->isExpired() === true) {
            throw new AccessDeniedException();
        }

        return true;
    }

    /**
     * Reads in the access token from the headers
     *
     * @param bool $headerOnly Limit Access Token to Authorization header
     *
     * @throws \OAuth2\Server\Exception\InvalidRequestException Thrown if there is no access token presented
     *
     * @return string
     */
    public function determineAccessToken($headerOnly = FALSE)
    {
		switch ($headerOnly){
			case FALSE:
				$accessToken = $this->requestHandler->getParam($this->tokenKey);
				if ($accessToken !== null){
					break;
				}
			case TRUE:
				$tokenType = $this->getTokenType();
				if (strrpos(get_class($tokenType),"Bearer") !== FALSE){
					$accessToken = $tokenType->determineAccessTokenInHeader($this->requestHandler,$this->authHeader);
				}else{
					$accessToken = $tokenType->determineAccessTokenInHeader($this->requestHandler);
				}
				break;
		}

        if (empty($accessToken)) {
            throw new InvalidRequestException('access token');
        }

        return $accessToken;
    }
}
