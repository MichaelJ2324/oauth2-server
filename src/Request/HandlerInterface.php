<?php
/**
 * Oauth 2.0 Request Handler
 */

namespace OAuth2\Server\Request;



interface HandlerInterface {

	/**
	 * Get a parameter value from the request
	 *
	 * @param string $param The parameter in the request
	 *
	 * @return mixed Value of the parameter in the request Or NULL
	 */
	public function getParam($param);
	/**
	 * Get a header value from the request
	 *
	 * @param string $header The header in the request
	 *
	 * @return mixed Value of the header in the request Or NULL
	 */
	public function getHeader($header);

	/**
	 * Get a header value from the request
	 *
	 * @return string HTTP Request Method
	 */
	public function getMethod();
	/**
	 * Get a header value from the request
	 *
	 * @return string URI of the server request
	 */
	public function getUri();
	/**
	 * Get a header value from the request
	 *
	 * @return string Hostname used in request
	 */
	public function getHost();
	/**
	 * Get a header value from the request
	 *
	 * @return string Port Number used in request
	 */
	public function getPort();

}