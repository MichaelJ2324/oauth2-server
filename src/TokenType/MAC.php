<?php
/**
 * OAuth 2.0 MAC Token Type
 *
 */

namespace OAuth2\Server\TokenType;

use OAuth2\Server\Util\SecureKey;
use OAuth2\Server\Request\HandlerInterface as RequestHandler;
/**
 * MAC Token Type
 */
class MAC extends AbstractTokenType implements TokenTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function generateResponse()
    {
        $macKey = SecureKey::generate();
        $this->server->getMacStorage()->create($macKey, $this->getParam('access_token'));

        $response = [
            'access_token'  =>  $this->getParam('access_token'),
            'token_type'    =>  'mac',
            'expires_in'    =>  $this->getParam('expires_in'),
            'mac_key'       =>  $macKey,
            'mac_algorithm' =>  'hmac-sha-256',
        ];

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function determineAccessTokenInHeader(RequestHandler $requestHandler,$authHeader = 'Authorization')
    {
        $header = $requestHandler->getHeader($authHeader);

		if ($header === null){
			return;
		}

        if (substr($header, 0, 4) !== 'MAC ') {
            return;
        }

        // Find all the parameters expressed in the header
        $paramsRaw = explode(',', substr($header, 4));
        $params = array();

        array_map(function ($param) use (&$params) {
            $param = trim($param);

            preg_match_all('/([a-zA-Z]*)="([\w=]*)"/', $param, $matches);

            // @codeCoverageIgnoreStart
            if (count($matches) !== 3) {
                return;
            }
            // @codeCoverageIgnoreEnd

            $key = reset($matches[1]);
            $value = trim(reset($matches[2]));

            if (empty($value)) {
                return;
            }

            $params[$key] = $value;
        }, $paramsRaw);

        // Validate parameters
        if (array_key_exists('id',$params) === false || array_key_exists('ts',$params) === false || array_key_exists('nonce',$params) === false || array_key_exists('mac',$params) === false) {
            return;
        }

        if ((int) $params['ts'] !== time()) {
            return;
        }

        $accessToken = $params['id'];
        $timestamp = (int) $params['ts'];
        $nonce = $params['nonce'];
        $signature = $params['mac'];

        // Try to find the MAC key for the access token
        $macKey = $this->server->getMacStorage()->getByAccessToken($accessToken);

        if ($macKey === null) {
            return;
        }

        // Calculate and compare the signature
        $calculatedSignatureParts = [
            $timestamp,
            $nonce,
            strtoupper($requestHandler->getMethod()),
            $requestHandler->getUri(),
			$requestHandler->getHost(),
			$requestHandler->getPort(),
        ];

        if (array_key_exists('ext',$params)) {
            $calculatedSignatureParts[] = $params['ext'];
        }

        $calculatedSignature = base64_encode(hash_hmac('sha256', implode("\n", $calculatedSignatureParts), $macKey));

        // Return the access token if the signature matches
        return ($this->hash_equals($calculatedSignature, $signature)) ? $accessToken : null;
    }

    /**
     * Prevent timing attack
     * @param  string $knownString
     * @param  string $userString
     * @return bool
     */
    private function hash_equals($knownString, $userString)
    {
        if (function_exists('\hash_equals')) {
            return \hash_equals($knownString, $userString);
        }
        if (strlen($knownString) !== strlen($userString)) {
            return false;
        }
        $len = strlen($knownString);
        $result = 0;
        for ($i = 0; $i < $len; $i++) {
            $result |= (ord($knownString[$i]) ^ ord($userString[$i]));
        }
        // They are only identical strings if $result is exactly 0...
        return 0 === $result;
    }
}
