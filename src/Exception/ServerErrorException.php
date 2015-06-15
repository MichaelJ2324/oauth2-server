<?php
/**
 * OAuth 2.0 Server Error Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class ServerErrorException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 500;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'server_error';

    /**
     * {@inheritdoc}
     */
    public function __construct($parameter = null)
    {
        $parameter = is_null($parameter) ? 'The authorization server encountered an unexpected condition which prevented it from fulfilling the request.' : $parameter;
        parent::__construct($parameter);
    }
}
