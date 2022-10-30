<?php

namespace jalsoedesign\NoDependencyHttpServer\packet;

use jalsoedesign\NoDependencyHttpServer\util\HttpUtil;

class HttpRequest {
	/** @var string */
	protected $requestMethod;
	/** @var string */
	protected $requestUrl;
	/** @var string */
	protected $httpVersion;
	/** @var array */
	protected $headers = [];
	/** @var string */
	protected $body;
	/** @var \Socket */
	protected $socketConnection;

	/**
	 * @param \Socket $socketConnection
	 *
	 * @param string  $requestMethod
	 * @param string  $requestUrl
	 * @param string  $httpVersion
	 * @param array   $headers
	 * @param string  $body
	 */
	public function __construct($socketConnection, $requestMethod, $requestUrl, $httpVersion, $headers, $body) {
		$this->socketConnection = $socketConnection;

		$this->requestMethod = $requestMethod;
		$this->requestUrl    = $requestUrl;
		$this->httpVersion   = $httpVersion;

		foreach ($headers as $header => $value) {
			$this->headers[ HttpUtil::normalizeHeader($header) ] = $value;
		}

		$this->body = $body;
	}

	/**
	 * @param \Socket $socketConnection
	 * @param string  $rawRequest
	 *
	 * @return HttpRequest
	 */
	public static function fromRawRequest($socketConnection, $rawRequest) {
		// Split initial package
		$firstLineEnd = strpos($rawRequest, "\r\n");
		$firstLine    = substr($rawRequest, 0, $firstLineEnd);
		$headersEnd   = strpos($rawRequest, "\r\n\r\n", $firstLineEnd);

		if ($headersEnd !== false) {
			$headersRaw = substr($rawRequest, $firstLineEnd + 2, $headersEnd - $firstLineEnd - 2);
			$body       = substr($rawRequest, $headersEnd + 4);
		} else {
			$headersRaw = substr($rawRequest, $firstLineEnd + 2);
			$body       = '';
		}

		// Compute request method, URL and http version
		if (preg_match('~^([A-Z]+)\s+(.+)\s+HTTP/(\d\.\d)$~', $firstLine, $match)) {
			$requestMethod = $match[1];
			$requestUrl    = $match[2];
			$httpVersion   = $match[3];
		} else {
			throw new \Exception(sprintf('Could not understand first line of HTTP request'));
		}

		// Calculate headers
		$headers = [];

		$headerLines = explode("\r\n", $headersRaw);

		foreach ($headerLines as $headerRawLine) {
			$headerRawLineColon = strpos($headerRawLine, ':');
			$header             = substr($headerRawLine, 0, $headerRawLineColon);
			$headerValue        = substr($headerRawLine, $headerRawLineColon + 2);

			$headers[ $header ] = $headerValue;
		}

		// Return response
		return new HttpRequest($socketConnection, $requestMethod, $requestUrl, $httpVersion, $headers, $body);
	}

	/**
	 * @return string
	 */
	public function getRequestMethod() {
		return $this->requestMethod;
	}

	/**
	 * @return string
	 */
	public function getRequestUrl() {
		return $this->requestUrl;
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param string $header
	 * @param mixed  $default
	 *
	 * @return string
	 */
	public function getHeader($header, $default = null) {
		$header = HttpUtil::normalizeHeader($header);

		return array_key_exists($header, $this->headers) ? $this->headers[ $header ] : $default;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param HttpResponse $httpResponse
	 *
	 * @return void
	 */
	public function sendResponse(HttpResponse $response) {
		$response->setHttpVersion($this->getHttpVersion());

		$responseString = $response->toString();

		socket_write($this->socketConnection, $responseString);
	}

	/**
	 * @return string
	 */
	public function getHttpVersion() {
		return $this->httpVersion;
	}
}
