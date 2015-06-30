<?php
/**
 * OAuth 2.0 Client credentials grant
 *
 */

namespace OAuth2\Server\Grant;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\ClientEntity;
use OAuth2\Server\Entity\SessionEntity;
use OAuth2\Server\Exception;
use OAuth2\Server\Util\SecureKey;

/**
 * Client credentials grant class
 */
class ClientCredentialsGrant extends AbstractGrant
{
    /**
     * Grant identifier
     *
     * @var string
     */
    protected $identifier = 'client_credentials';

    /**
     * Response type
     *
     * @var string
     */
    protected $responseType = null;

    /**
     * AuthServer instance
     *
     * @var \OAuth2\Server\AuthorizationServer
     */
    protected $server = null;

    /**
     * Access token expires in override
     *
     * @var int
     */
    protected $accessTokenTTL = null;

    /**
     * Complete the client credentials grant
     *
     * @return array
     *
     * @throws
     */
    public function completeFlow(ClientEntity $client)
    {
        // Validate any scopes that are in the request
        $scopeParam = $this->server->getRequestHandler()->getParam('scope');
        $scopes = $this->validateScopes($scopeParam, $client);

        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner('client', $client->getId());
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

        // Save everything
        $session->save();
        $accessToken->setSession($session);
        $accessToken->save();

        $this->server->getTokenType()->setSession($session);
        $this->server->getTokenType()->setParam('access_token', $accessToken->getId());
        $this->server->getTokenType()->setParam('expires_in', $this->getAccessTokenTTL());

        return $this->server->getTokenType()->generateResponse();
    }
}
