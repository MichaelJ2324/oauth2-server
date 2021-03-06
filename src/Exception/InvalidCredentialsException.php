<?php
/**
 * OAuth 2.0 Invalid Credentials Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class InvalidCredentialsException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 401;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'invalid_credentials';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('The user credentials were incorrect.');
    }
}
