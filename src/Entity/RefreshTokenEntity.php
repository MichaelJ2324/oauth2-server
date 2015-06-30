<?php
/**
 * OAuth 2.0 Refresh token entity
 *
 */

namespace OAuth2\Server\Entity;

/**
 * Refresh token entity class
 */
class RefreshTokenEntity extends AbstractTokenEntity
{
    /**
     * Access token associated to refresh token
     *
     * @var \OAuth2\Server\Entity\AccessTokenEntity
     */
    protected $accessTokenEntity;

    /**
     * Id of the access token
     *
     * @var string
     */
    protected $accessTokenId;

    /**
     * Set the ID of the associated access token
     *
     * @param string $accessTokenId
     *
     * @return self
     */
    public function setAccessTokenId($accessTokenId)
    {
        $this->accessTokenId = $accessTokenId;

        return $this;
    }

    /**
     * Associate an access token
     *
     * @param \OAuth2\Server\Entity\AccessTokenEntity $accessTokenEntity
     *
     * @return self
     */
    public function setAccessToken(AccessTokenEntity $accessTokenEntity)
    {
        $this->accessTokenEntity = $accessTokenEntity;

        return $this;
    }

    /**
     * Return access token
     *
     * @return AccessTokenEntity
     */
    public function getAccessToken()
    {
        if (! $this->accessTokenEntity instanceof AccessTokenEntity) {
            $this->accessTokenEntity = $this->server->getAccessTokenStorage()->get($this->accessTokenId);
        }

        return $this->accessTokenEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
		if ($this->isNew) {
			$this->server->getRefreshTokenStorage()->create(
				$this->getId(),
				$this->getExpireTime(),
				$this->getAccessToken()->getId()
			);
		}else{
			$this->server->getRefreshTokenStorage()->save($this);
		}
    }

    /**
     * {@inheritdoc}
     */
    public function expire()
    {
        $this->server->getRefreshTokenStorage()->delete($this);
    }
}
