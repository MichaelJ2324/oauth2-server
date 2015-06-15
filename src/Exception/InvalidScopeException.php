<?php
/**
 * OAuth 2.0 Invalid Scope Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class InvalidScopeException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'invalid_scope';

    /**
     * {@inheritdoc}
     */

    public function __construct($parameter, $redirectUri = null)
    {
        parent::__construct(
            sprintf(
                'The requested scope is invalid, unknown, or malformed. Check the "%s" scope.',
                $parameter
            )
        );

        $this->redirectUri = $redirectUri;
    }
}
