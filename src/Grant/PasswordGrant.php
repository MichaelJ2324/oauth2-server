<?php
/**
 * OAuth 2.0 Password grant
 *
 */

namespace OAuth2\Server\Grant;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\ClientEntity;
use OAuth2\Server\Entity\RefreshTokenEntity;
use OAuth2\Server\Entity\SessionEntity;
use OAuth2\Server\Exception;
use OAuth2\Server\Util\SecureKey;

/**
 * Password grant class
 */
class PasswordGrant extends AbstractGrant
{
    /**
     * Grant identifier
     *
     * @var string
     */
    protected $identifier = 'password';

    /**
     * Response type
     *
     * @var string
     */
    protected $responseType;

    /**
     * Callback to authenticate a user's name and password
     *
     * @var callable
     */
    protected $callback;

    /**
     * Access token expires in override
     *
     * @var int
     */
    protected $accessTokenTTL;

    /**
     * Set the callback to verify a user's username and password
     *
     * @param callable $callback The callback function
     *
     * @return void
     */
    public function setVerifyCredentialsCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Return the callback function
     *
     * @return callable
     *
     * @throws
     */
    protected function getVerifyCredentialsCallback()
    {
        if (is_null($this->callback) || !is_callable($this->callback)) {
            throw new Exception\ServerErrorException('Null or non-callable callback set on Password grant');
        }

        return $this->callback;
    }

    /**
     * Complete the password grant
     *
     * @return array
     *
     * @throws
     */
    public function completeFlow(ClientEntity $client)
    {
        $username = $this->server->getRequestHandler()->getParam('username');
        if (is_null($username)) {
            throw new Exception\InvalidRequestException('username');
        }

        $password = $this->server->getRequestHandler()->getParam('password');
        if (is_null($password)) {
            throw new Exception\InvalidRequestException('password');
        }

        // Check if user's username and password are correct
        $userId = call_user_func($this->getVerifyCredentialsCallback(), $username, $password);

        if ($userId === false) {
            throw new Exception\InvalidCredentialsException();
        }

        // Validate any scopes that are in the request
        $scopeParam = $this->server->getRequestHandler()->getParam('scope');
        $scopes = $this->validateScopes($scopeParam, $client);

        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner('user', $userId);
        $session->associateClient($client);

        // Generate an access token
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId();
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        // Associate scopes with the session and access token
        foreach ($scopes as $scope) {
            $session->associateScope($scope);
        }

        foreach ($session->getScopes() as $scope) {
            $accessToken->associateScope($scope);
        }

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $accessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        // Associate a refresh token if set
        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken = new RefreshTokenEntity($this->server);
            $refreshToken->setId();
            $refreshToken->setExpireTime($this->server->getGrantType('refresh_token')->getRefreshTokenTTL() + time());
            $this->server->getTokenType()->setParam('refresh_token', $refreshToken->getId());
        }

        // Save everything
        $session->save();
        $accessToken->setSession($session);
        $accessToken->save();

        if ($this->server->hasGrantType('refresh_token')) {
            $refreshToken->setAccessToken($accessToken);
            $refreshToken->save();
        }

        return $this->server->getTokenType()->generateResponse();
    }
}
