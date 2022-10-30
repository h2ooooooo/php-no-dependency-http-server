<?php

namespace jalsoedesign\NoDependencyHttpServer\util;

class HttpUtil {
	/**
	 * @param string $header
	 *
	 * @return string
	 */
	public static function normalizeHeader($header) {
		$headerParts = explode('-', $header);

		foreach ($headerParts as $i => $headerPart) {
			$headerParts[ $i ] = ucfirst(strtolower($headerPart));
		}

		return implode('-', $headerParts);
	}
}
