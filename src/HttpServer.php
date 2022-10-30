<?php

namespace jalsoedesign\NoDependencyHttpServer;

use jalsoedesign\NoDependencyHttpServer\options\HttpServerOptions;
use jalsoedesign\NoDependencyHttpServer\packet\HttpRequest;
use jalsoedesign\NoDependencyHttpServer\packet\HttpResponse;
use jalsoedesign\NoDependencyHttpServer\statusCode\StatusCode;

class HttpServer {
	/** @var int */
	const DEFAULT_PORT = 5000;
	/** @var string */
	const DEFAULT_HOST = '127.0.0.1';
	protected $options;
	/** @var bool */
	protected $isRunning = true;
	/** @var \Socket */
	protected $socket;

	/**
	 * @param HttpServerOptions $options
	 */
	public function __construct($options = null) {
		if ( ! empty($options)) {
			$this->options = $options;
		} else {
			$this->options = new HttpServerOptions();
		}
	}

	/**
	 * @param int               $port
	 * @param string            $host
	 * @param HttpServerOptions $options
	 *
	 * @throws \Exception
	 */
	public static function serveFiles(
		$serveDirectory,
		$port = HttpServer::DEFAULT_PORT,
		$host = HttpServer::DEFAULT_HOST,
		$options = null
	) {
		if ( ! file_exists($serveDirectory)) {
			throw new \Exception(sprintf('Could not find directory at %s', $serveDirectory));
		}

		static::infinite(function(HttpRequest $request) use ($serveDirectory) {
			$requestUrl = $request->getRequestUrl();

			// Make sure /../../../secret.cert isn't accessible
			$requestUrl = preg_replace('~\.+/~', '', $requestUrl);

			$requestPath = $serveDirectory . $requestUrl;

			if ( ! file_exists($requestPath)) {
				return new HttpResponse(StatusCode::NOT_FOUND,
					sprintf('Could not find any files at URL %s', $requestUrl));
			}

			$mimeContentType = @mime_content_type($requestPath);

			$headers = [];

			if (!empty($mimeContentType)) {
				$headers['Content-Type'] = $mimeContentType;
			} else {
				$headers['Content-Type']        = 'application/octet-stream';
				$headers['Content-Disposition'] = 'attachment';
			}

			return new HttpResponse(StatusCode::OK, file_get_contents($requestPath), $headers);
		});
	}

	/**
	 * @param callable          $callback
	 * @param int               $port
	 * @param string            $host
	 * @param HttpServerOptions $options
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function infinite(
		$callback,
		$port = HttpServer::DEFAULT_PORT,
		$host = HttpServer::DEFAULT_HOST,
		$options = null
	) {
		$server = new HttpServer($options);

		$server->listen($port, $host);

		foreach ($server->requestGenerator() as $request) {
			$response = call_user_func($callback, $request);

			if ($response === false) {
				break;
			} else if ($response instanceof HttpResponse) {
				$request->sendResponse($response);
			}
		}
	}

	/**
	 * @param int    $port
	 * @param string $host
	 *
	 * @return void
	 */
	public function listen($port = HttpServer::DEFAULT_PORT, $host = HttpServer::DEFAULT_HOST) {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

		socket_bind($socket, $host, $port);

		socket_listen($socket, $this->options->getIncomingClientsBacklog());

		socket_set_nonblock($socket);

		$this->socket = $socket;
	}

	/**
	 * @return \Generator|HttpRequest
	 *
	 * @throws \Exception
	 */
	public function requestGenerator() {
		while ($this->isRunning()) {
			$socketConnection = @socket_accept($this->socket);

			if ($socketConnection === false) {
				// Chill a little bit
				usleep($this->options->getUsleepTime());
			} else if ($socketConnection > 0) {
				$buffer = '';

				$readBufferSize = $this->options->getReadBufferSize();

				while ($read = socket_read($socketConnection, $readBufferSize)) {
					$read = trim($read);

					if ( ! empty($read)) {
						$buffer .= $read;
					}
				}

				if ( ! empty($buffer)) {
					$request = HttpRequest::fromRawRequest($socketConnection, $buffer);

					yield $request;

					// Kick them, this isn't a keep-alive server
					socket_close($socketConnection);
				}
			} else {
				$socketError       = socket_last_error($socketConnection);
				$socketErrorString = socket_strerror($socketError);

				socket_clear_error($this->socket);

				throw new \Exception(sprintf('%s (#%d)', $socketErrorString, $socketError));
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isRunning() {
		return $this->isRunning;
	}

	/**
	 * @param callable          $callback
	 * @param int               $port
	 * @param string            $host
	 * @param HttpServerOptions $options
	 *
	 * @return void
	 *
	 * @throws \Exception
	 */
	public static function once(
		$callback,
		$port = HttpServer::DEFAULT_PORT,
		$host = HttpServer::DEFAULT_HOST,
		$options = null
	) {
		$request = static::getRequest($port, $host, $options);

		$response = call_user_func($callback, $request);

		$request->sendResponse($response);

		return $response;
	}

	/**
	 * @param int               $port
	 * @param string            $host
	 * @param HttpServerOptions $options
	 *
	 * @return HttpRequest
	 *
	 * @throws \Exception
	 */
	public static function getRequest(
		$port = HttpServer::DEFAULT_PORT,
		$host = HttpServer::DEFAULT_HOST,
		$options = null
	) {
		$server = new HttpServer($options);

		$server->listen($port, $host);

		foreach ($server->requestGenerator() as $request) {
			return $request;
		}
	}
}
