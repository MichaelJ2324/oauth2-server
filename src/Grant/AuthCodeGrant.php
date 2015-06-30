<?php
/**
 * OAuth 2.0 Auth code grant
 *
 */

namespace OAuth2\Server\Grant;

use OAuth2\Server\Entity\AccessTokenEntity;
use OAuth2\Server\Entity\AuthCodeEntity;
use OAuth2\Server\Entity\ClientEntity;
use OAuth2\Server\Entity\RefreshTokenEntity;
use OAuth2\Server\Entity\SessionEntity;
use OAuth2\Server\Exception;
use OAuth2\Server\Util\SecureKey;

/**
 * Auth code grant class
 */
class AuthCodeGrant extends AbstractGrant
{
    /**
     * Grant identifier
     *
     * @var string
     */
    protected $identifier = 'authorization_code';

    /**
     * Response type
     *
     * @var string
     */
    protected $responseType = 'code';

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
     * The TTL of the auth token
     *
     * @var integer
     */
    protected $authTokenTTL = 600;

    /**
     * Override the default access token expire time
     *
     * @param int $authTokenTTL
     *
     * @return void
     */
    public function setAuthTokenTTL($authTokenTTL)
    {
        $this->authTokenTTL = $authTokenTTL;
    }

    /**
     * Check authorize parameters
     *
     * @return array Authorize request parameters
     *
     * @throws
     */
    public function checkAuthorizeParams()
    {
        // Get required params
        $clientId = $this->server->getRequestHandler()->getParam('client_id');
        if (is_null($clientId)) {
            throw new Exception\InvalidRequestException('client_id');
        }

        $redirectUri = $this->server->getRequestHandler()->getParam('redirect_uri');
        if (is_null($redirectUri)) {
            throw new Exception\InvalidRequestException('redirect_uri');
        }

        // Validate client ID and redirect URI
        $client = $this->server->getClientStorage()->get(
            $clientId,
            null,
            $redirectUri,
            $this->getIdentifier()
        );

        if (($client instanceof ClientEntity) === false) {
            throw new Exception\InvalidClientException();
        }

        $state = $this->server->getRequestHandler()->getParam('state');
        if ($this->server->stateParamRequired() === true && is_null($state)) {
            throw new Exception\InvalidRequestException('state', $redirectUri);
        }

        $responseType = $this->server->getRequestHandler()->getParam('response_type');
        if (is_null($responseType)) {
            throw new Exception\InvalidRequestException('response_type', $redirectUri);
        }

        // Ensure response type is one that is recognised
        if (!in_array($responseType, $this->server->getResponseTypes())) {
            throw new Exception\UnsupportedResponseTypeException($responseType, $redirectUri);
        }

        // Validate any scopes that are in the request
        $scopeParam = $this->server->getRequestHandler()->getParam('scope');
        $scopes = $this->validateScopes($scopeParam, $client, $redirectUri);

        return array(
            'client'        => $client,
            'redirect_uri'  => $redirectUri,
            'state'         => $state,
            'response_type' => $responseType,
            'scopes'        => $scopes
		);
    }

    /**
     * Parse a new authorize request
     *
     * @param string $type       The session owner's type
     * @param string $typeId     The session owner's ID
     * @param array  $authParams The authorize request $_GET parameters
     *
     * @return string An authorisation code
     */
    public function newAuthorizeRequest($type, $typeId, $authParams = [])
    {
        // Create a new session
        $session = new SessionEntity($this->server);
        $session->setOwner($type, $typeId);
        $session->associateClient($authParams['client']);

        // Create a new auth code
        $authCode = new AuthCodeEntity($this->server);
        $authCode->setId();
        $authCode->setRedirectUri($authParams['redirect_uri']);
        $authCode->setExpireTime(time() + $this->authTokenTTL);

        foreach ($authParams['scopes'] as $scope) {
            $authCode->associateScope($scope);
            $session->associateScope($scope);
        }

        $session->save();
        $authCode->setSession($session);
        $authCode->save();

        return $authCode->generateRedirectUri($authParams['state']);
    }

    /**
     * Complete the auth code grant
     *
     * @return array
     *
     * @throws
     */
    public function completeFlow(ClientEntity $client)
    {
        // Validate the auth code
        $authCode = $this->server->getRequestHandler()->getParam('code');
        if (is_null($authCode)) {
            throw new Exception\InvalidRequestException('code');
        }

        $code = $this->server->getAuthCodeStorage()->get($authCode);
        if (($code instanceof AuthCodeEntity) === false) {
            throw new Exception\InvalidRequestException('code');
        }

        // Ensure the auth code hasn't expired
        if ($code->isExpired() === true) {
            throw new Exception\InvalidRequestException('code');
        }

        // Check redirect URI presented matches redirect URI originally used in authorize request
        if ($code->getRedirectUri() !== $client->getRedirectUri()) {
            throw new Exception\InvalidRequestException('redirect_uri');
        }

        $session = $code->getSession();
        $session->associateClient($client);

        $authCodeScopes = $code->getScopes();

        // Generate the access token
        $accessToken = new AccessTokenEntity($this->server);
        $accessToken->setId();
        $accessToken->setExpireTime($this->getAccessTokenTTL() + time());

        foreach ($authCodeScopes as $authCodeScope) {
            $session->associateScope($authCodeScope);
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

        // Expire the auth code
        $code->expire();

        // Save all the things
        $accessToken->setSession($session);
        $accessToken->save();

        if (isset($refreshToken) && $this->server->hasGrantType('refresh_token')) {
            $refreshToken->setAccessToken($accessToken);
            $refreshToken->save();
        }

        return $this->server->getTokenType()->generateResponse();
    }
}
