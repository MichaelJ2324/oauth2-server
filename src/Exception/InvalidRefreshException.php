<?php
/**
 * OAuth 2.0 Invalid Refresh Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class InvalidRefreshException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'invalid_request';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('The refresh token is invalid.');
    }
}
