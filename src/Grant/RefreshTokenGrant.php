<?php
/**
 * OAuth 2.0 Refresh token grant
 *
 */

namespace OAuth2\Server\Grant;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\ClientEntity;
use OAuth2\Server\Entity\RefreshTokenEntity;
use OAuth2\Server\Event;
use OAuth2\Server\Exception;
use OAuth2\Server\Util\SecureKey;

/**
 * Referesh token grant
 */
class RefreshTokenGrant extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    protected $identifier = 'refresh_token';

    /**
     * Refresh token TTL (default = 604800 | 1 week)
     *
     * @var integer
     */
    protected $refreshTokenTTL = 604800;

    /**
     * Rotate token (default = true)
     *
     * @var integer
     */
    protected $refreshTokenRotate = false;

    /**
     * Set the TTL of the refresh token
     *
     * @param int $refreshTokenTTL
     *
     * @return void
     */
    public function setRefreshTokenTTL($refreshTokenTTL)
    {
        $this->refreshTokenTTL = $refreshTokenTTL;
    }

    /**
     * Get the TTL of the refresh token
     *
     * @return int
     */
    public function getRefreshTokenTTL()
    {
        return $this->refreshTokenTTL;
    }

    /**
     * Set the rotation boolean of the refresh token
     * @param bool $refreshTokenRotate
     */
    public function setRefreshTokenRotation($refreshTokenRotate = true)
    {
        $this->refreshTokenRotate = $refreshTokenRotate;
    }

    /**
     * Get rotation boolean of the refresh token
     *
     * @return bool
     */
    public function shouldRotateRefreshTokens()
    {
        return $this->refreshTokenRotate;
    }

    /**
     * {@inheritdoc}
     */
    public function completeFlow(ClientEntity $client)
    {
        $oldRefreshTokenParam = $this->server->getRequestHandler()->getParam('refresh_token');
        if ($oldRefreshTokenParam === null) {
            throw new Exception\InvalidRequestException('refresh_token');
        }

        // Validate refresh token
        $oldRefreshToken = $this->server->getRefreshTokenStorage()->get($oldRefreshTokenParam);

        if (($oldRefreshToken instanceof RefreshTokenEntity) === false) {
            throw new Exception\InvalidRefreshException();
        }

        // Ensure the old refresh token hasn't expired
        if ($oldRefreshToken->isExpired() === true) {
            throw new Exception\InvalidRefreshException();
        }

        $oldAccessToken = $oldRefreshToken->getAccessToken();

        // Get the scopes for the original session
        $session = $oldAccessToken->getSession();
        $scopes = $this->formatScopes($session->getScopes());

        // Get and validate any requested scopes
        $requestedScopesString = $this->server->getRequestHandler()->getParam('scope');
        $requestedScopes = $this->validateScopes($requestedScopesString, $client);

        // If no new scopes are requested then give the access token the original session scopes
        if (count($requestedScopes) === 0) {
            $newScopes = $scopes;
        } else {
            // The OAuth spec says that a refreshed access token can have the original scopes or fewer so ensure
            //  the request doesn't include any new scopes
            foreach ($requestedScopes as $requestedScope) {
                if (!isset($scopes[$requestedScope->getId()])) {
                    throw new Exception\InvalidScopeException($requestedScope->getId());
                }
            }

            $newScopes = $requestedScopes;
        }

        // Generate a new access token and assign it the correct sessions
        $newAccessToken = new AccessTokenEntity($this->server);
        $newAccessToken->setId(SecureKey::generate());
        $newAccessToken->setExpireTime($this->getAccessTokenTTL() + time());
        $newAccessToken->setSession($session);

        foreach ($newScopes as $newScope) {
            $newAccessToken->associateScope($newScope);
        }

		//Save new access token
        $newAccessToken->save();

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $newAccessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        if ($this->shouldRotateRefreshTokens()) {
            // Expire the old refresh token
            $oldRefreshToken->expire();

            // Generate a new refresh token
            $newRefreshToken = new RefreshTokenEntity($this->server);
            $newRefreshToken->setId(SecureKey::generate());
            $newRefreshToken->setExpireTime($this->getRefreshTokenTTL() + time());
            $newRefreshToken->setAccessToken($newAccessToken);
            $newRefreshToken->save();

            $this->server->getTokenType()->setParam('refresh_token', $newRefreshToken->getId());
        } else {
			$oldRefreshToken->setAccessToken($newAccessToken);
			$oldRefreshToken->save();
            $this->server->getTokenType()->setParam('refresh_token', $oldRefreshToken->getId());
        }
		// Expire the old token
		$oldAccessToken->expire();

        return $this->server->getTokenType()->generateResponse();
    }
}
