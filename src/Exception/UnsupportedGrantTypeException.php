<?php
/**
 * OAuth 2.0 Invalid Request Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class UnsupportedGrantTypeException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unsupported_grant_type';

    /**
     * {@inheritdoc}
     */

    public function __construct($parameter)
    {
        parent::__construct(
            sprintf(
                'The authorization grant type "%s" is not supported by the authorization server.',
                $parameter
            )
        );
    }
}
