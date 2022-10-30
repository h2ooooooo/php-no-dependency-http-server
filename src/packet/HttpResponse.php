<?php

namespace jalsoedesign\NoDependencyHttpServer\packet;

use jalsoedesign\NoDependencyHttpServer\statusCode\StatusCodeMessage;
use jalsoedesign\NoDependencyHttpServer\util\HttpUtil;

class HttpResponse {
	/** @var string */
	protected $statusCode;
	/** @var string */
	protected $body;
	/** @var string */
	protected $httpVersion;
	/** @var array */
	protected $headers = [];

	/**
	 * @param int    $statusCode
	 * @param string $body
	 * @param array  $headers
	 * @param string $httpVersion
	 */
	public function __construct($statusCode, $body = null, $headers = [], $httpVersion = null) {
		$this->statusCode = $statusCode;

		if ($body !== null) {
			$this->setBody($body);
		}

		if ( ! empty($httpVersion)) {
			$this->setHttpVersion($httpVersion);
		}

		if ( ! empty($headers)) {
			$this->setHeaders($headers);
		}
	}

	public static function ok($message) {
		return new HttpResponse(200, $message);
	}

	/**
	 * @return string
	 */
	public function toString() {
		$httpResponse = sprintf('HTTP/%s %d %s', $this->getHttpVersion(), $this->getStatusCode(),
			$this->getStatusMessage());

		$bodyLength = $this->body !== null ? strlen($this->body) : 0;

		$this->setHeader('Content-Length', $bodyLength);

		foreach ($this->headers as $header => $value) {
			$httpResponse .= sprintf("\r\n%s: %s", $header, $value);
		}

		if ($this->body !== null) {
			$httpResponse .= "\r\n\r\n" . $this->body;
		}

		return $httpResponse;
	}

	/**
	 * @return string
	 */
	public function getHttpVersion() {
		return $this->httpVersion;
	}

	public function setHttpVersion($httpVersion) {
		$this->httpVersion = $httpVersion;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @param int $statusCode
	 *
	 * @return void
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}

	/**
	 * @return string
	 */
	public function getStatusMessage() {
		return StatusCodeMessage::getMessageFromCode($this->getStatusCode());
	}

	/**
	 * @param string $header
	 * @param string $value
	 *
	 * @return void
	 */
	public function setHeader($header, $value) {
		$this->headers[ HttpUtil::normalizeHeader($header) ] = $value;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @param string $body
	 *
	 * @return void
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * @param array $headers
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function setHeaders($headers) {
		$this->headers = [];

		if ( ! empty($headers)) {
			if ( ! is_array($headers)) {
				throw new \Exception(sprintf('$headers must be an array'));
			}

			foreach ($headers as $header => $value) {
				$this->setHeader($header, $value);
			}
		}
	}
}
