<?php
/**
 * OAuth 2.0 Session storage interface
 *
 */

namespace OAuth2\Server\Storage;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\AuthCodeEntity;
use OAuth2\Server\Entity\ScopeEntity;
use OAuth2\Server\Entity\SessionEntity;

/**
 * Session storage interface
 */
interface SessionInterface extends StorageInterface
{
    /**
     * Get a session from an access token
     *
     * @param \OAuth2\Server\Entity\AccessTokenEntity $accessToken The access token
     *
     * @return \OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAccessToken(AccessTokenEntity $accessToken);

    /**
     * Get a session from an auth code
     *
     * @param \OAuth2\Server\Entity\AuthCodeEntity $authCode The auth code
     *
     * @return \OAuth2\Server\Entity\SessionEntity | null
     */
    public function getByAuthCode(AuthCodeEntity $authCode);

    /**
     * Get a session's scopes
     *
     * @param  \OAuth2\Server\Entity\SessionEntity
     *
     * @return \OAuth2\Server\Entity\ScopeEntity[] Array of \OAuth2\Server\Entity\ScopeEntity
     */
    public function getScopes(SessionEntity $session);

    /**
     * Create a new session
     *
     * @param string $ownerType         Session owner's type (user, client)
     * @param string $ownerId           Session owner's ID
     * @param string $clientId          Client ID
     * @param string $clientRedirectUri Client redirect URI (default = null)
     *
     * @return integer The session's ID
     */
    public function create($ownerType, $ownerId, $clientId, $clientRedirectUri = null);

    /**
     * Associate a scope with a session
     *
     * @param \OAuth2\Server\Entity\SessionEntity $session The session
     * @param \OAuth2\Server\Entity\ScopeEntity   $scope   The scope
     *
     * @return void
     */
    public function associateScope(SessionEntity $session, ScopeEntity $scope);
}
