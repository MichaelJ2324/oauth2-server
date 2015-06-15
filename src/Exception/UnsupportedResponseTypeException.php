<?php
/**
 * OAuth 2.0 Unsupported Response Type Exception
 *
 */

namespace OAuth2\Server\Exception;

/**
 * Exception class
 */
class UnsupportedResponseTypeException extends OAuthException
{
    /**
     * {@inheritdoc}
     */
    public $httpStatusCode = 400;

    /**
     * {@inheritdoc}
     */
    public $errorType = 'unsupported_response_type';

    /**
     * {@inheritdoc}
     */
    public function __construct($parameter, $redirectUri = null)
    {
        parent::__construct('The authorization server does not support obtaining an access token using this method.');
        $this->redirectUri = $redirectUri;
    }
}
