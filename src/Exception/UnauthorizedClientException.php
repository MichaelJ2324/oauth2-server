<?php
/**
 * OAuth 2.0 Unauthorized Client Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class UnauthorizedClientException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unauthorized_client';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('The client is not authorized to request an access token using this method.');
    }
}
