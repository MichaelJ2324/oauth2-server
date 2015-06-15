<?php
/**
 * OAuth 2.0 Invalid Client Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class InvalidClientException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 401;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'invalid_client';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('Client authentication failed.');
    }
}
