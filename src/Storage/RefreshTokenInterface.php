<?php
/**
 * OAuth 2.0 Refresh token storage interface
 *
 */

namespace OAuth2\Server\Storage;

use OAuth2\Server\Entity\RefreshTokenEntity;

/**
 * Refresh token interface
 */
interface RefreshTokenInterface extends StorageInterface
{
    /**
     * Return a new instance of \OAuth2\Server\Entity\RefreshTokenEntity
     *
     * @param string $token
     *
     * @return \OAuth2\Server\Entity\RefreshTokenEntity | null
     */
    public function get($token);

    /**
     * Create a new refresh token_name
     *
     * @param string  $token
     * @param integer $expireTime
     * @param string  $accessToken
     *
     * @return \OAuth2\Server\Entity\RefreshTokenEntity
     */
    public function create($token, $expireTime, $accessToken);

    /**
     * Delete the refresh token
     *
     * @param \OAuth2\Server\Entity\RefreshTokenEntity $token
     *
     * @return void
     */
    public function delete(RefreshTokenEntity $token);
}
