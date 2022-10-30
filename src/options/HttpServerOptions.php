<?php

namespace jalsoedesign\NoDependencyHttpServer\options;

class HttpServerOptions {
	protected $incomingClientsBacklog = 10;
	protected $usleepTime = 100;
	protected $readBufferSize = 1024;

	/**
	 * @return HttpServerOptions
	 */
	public static function build() {
		return new HttpServerOptions();
	}

	/**
	 * @return int
	 */
	public function getIncomingClientsBacklog() {
		return $this->incomingClientsBacklog;
	}

	/**
	 * @param int $incomingClientsBacklog
	 *
	 * @return $this
	 */
	public function setIncomingClientsBacklog($incomingClientsBacklog) {
		$this->incomingClientsBacklog = (int) $incomingClientsBacklog;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getUsleepTime() {
		return $this->usleepTime;
	}

	/**
	 * @param int $usleepTime
	 *
	 * @return $this
	 */
	public function setUsleepTime($usleepTime) {
		$this->usleepTime = (int) $usleepTime;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getReadBufferSize() {
		return $this->readBufferSize;
	}

	/**
	 * @param int $usleepTime
	 *
	 * @return $this
	 */
	public function setReadBufferSize($readBufferSize) {
		$this->readBufferSize = (int) $readBufferSize;

		return $this;
	}
}
