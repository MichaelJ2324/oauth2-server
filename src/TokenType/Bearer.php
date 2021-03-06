<?php
/**
 * OAuth 2.0 Bearer Token Type
 *
 */

namespace OAuth2\Server\TokenType;

use OAuth2\Server\Request\HandlerInterface as RequestHandler;

class Bearer extends AbstractTokenType implements TokenTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateResponse()
    {
        $return = [
            'access_token'  =>  $this->getParam('access_token'),
            'token_type'    =>  'Bearer',
            'expires_in'    =>  $this->getParam('expires_in'),
        ];

        if (!is_null($this->getParam('refresh_token'))) {
            $return['refresh_token'] = $this->getParam('refresh_token');
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function determineAccessTokenInHeader(RequestHandler $requestHandler, $authHeader = 'Authorization')
    {
        $header = $requestHandler->getHeader($authHeader);
        $accessToken = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header));

        return ($accessToken === 'Bearer') ? '' : $accessToken;
    }
}
