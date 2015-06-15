<?php
/**
 * OAuth 2.0 Secure key interface
 *
 */

namespace OAuth2\Server\Util\KeyAlgorithm;

interface KeyAlgorithmInterface
{
    /**
     * Generate a new unique code
     *
     * @param integer $len Length of the generated code
     *
     * @return string
     */
    public function generate($len);
}
