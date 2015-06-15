<?php
/**
 * OAuth 2.0 Access Denied Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class AccessDeniedException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 401;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'access_denied';

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct('The resource owner or authorization server denied the request.');
    }
}
