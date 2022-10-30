<?php

namespace jalsoedesign\NoDependencyHttpServer\statusCode;

class StatusCodeMessage {
	private static $statusCodeMessages = [
		// 1xx
		StatusCode::CONTINUE                        => 'Continue', // code 100
		StatusCode::SWITCHING_PROTOCOLS             => 'Switching Protocols', // code 101
		StatusCode::PROCESSING                      => 'Processing', // code 102
		StatusCode::EARLY_HINTS                     => 'Early Hints', // code 102

		// 2xx
		StatusCode::OK                              => 'OK', // code 200
		StatusCode::CREATED                         => 'Created', // code 201
		StatusCode::ACCEPTED                        => 'Accepted', // code 202
		StatusCode::NON_AUTHORITATIVE_INFORMATION   => 'Non-Authoritative Information', // code 203
		StatusCode::NO_CONTENT                      => 'No Content', // code 204
		StatusCode::RESET_CONTENT                   => 'Reset Content', // code 205
		StatusCode::PARTIAL_CONTENT                 => 'Partial Content', // code 206
		StatusCode::MULTI_STATUS                    => 'Multi-Status', // code 207
		StatusCode::ALREADY_REPORTED                => 'Already Reported', // code 208
		StatusCode::IM_USED                         => 'IM Used', // code 226

		// 3xx
		StatusCode::MULTIPLE_CHOICES                => 'Multiple Choices', // code 300
		StatusCode::MOVED_PERMANENTLY               => 'Moved Permanently', // code 301
		StatusCode::FOUND                           => 'Found', // code 302
		StatusCode::SEE_OTHER                       => 'See Other', // code 303
		StatusCode::NOT_MODIFIED                    => 'Not Modified', // code 304
		StatusCode::USE_PROXY                       => 'Use Proxy', // code 305
		StatusCode::SWITCH_PROXY                    => 'Switch Proxy', // code 306
		StatusCode::TEMPORARY_REDIRECT              => 'Temporary Redirect', // code 307
		StatusCode::PERMANENT_REDIRECT              => 'Permanent Redirect', // code 308

		// 4xx
		StatusCode::BAD_REQUEST                     => 'Bad Request', // code 400
		StatusCode::UNAUTHORIZED                    => 'Unauthorized', // code 401
		StatusCode::PAYMENT_REQUIRED                => 'Payment Required', // code 402
		StatusCode::FORBIDDEN                       => 'Forbidden', // code 403
		StatusCode::NOT_FOUND                       => 'Not Found', // code 404
		StatusCode::METHOD_NOT_ALLOWED              => 'Method Not Allowed', // code 405
		StatusCode::NOT_ACCEPTABLE                  => 'Not Acceptable', // code 406
		StatusCode::PROXY_AUTHENTICATION_REQUIRED   => 'Proxy Authentication Required', // code 407
		StatusCode::REQUEST_TIMEOUT                 => 'Request Timeout', // code 408
		StatusCode::CONFLICT                        => 'Conflict', // code 409
		StatusCode::GONE                            => 'Gone', // code 410
		StatusCode::LENGTH_REQUIRED                 => 'Length Required', // code 411
		StatusCode::PRECONDITION_FAILED             => 'Precondition Failed', // code 412
		StatusCode::REQUEST_ENTITY_TOO_LARGE        => 'Payload Too Large', // code 413
		StatusCode::REQUEST_URI_TOO_LONG            => 'URI Too Long', // code 414
		StatusCode::UNSUPPORTED_MEDIA_TYPE          => 'Unsupported Media Type', // code 415
		StatusCode::REQUESTED_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable', // code 416
		StatusCode::EXPECTATION_FAILED              => 'Expectation Failed', // code 417
		StatusCode::I_AM_A_TEAPOT                   => 'I\'m a teapot', // code 418
		StatusCode::MISDIRECTED_REQUEST             => 'Misdirected Request', // code 421
		StatusCode::UNPROCESSABLE_ENTITY            => 'Unprocessable Entity', // code 422
		StatusCode::LOCKED                          => 'Locked', // code 423
		StatusCode::FAILED_DEPENDENCY               => 'Failed Dependency', // code 424
		StatusCode::UNORDERED_COLLECTION            => 'Too Early', // code 425
		StatusCode::UPGRADE_REQUIRED                => 'Upgrade Required', // code 426
		StatusCode::PRECONDITION_REQUIRED           => 'Precondition Required', // code 428
		StatusCode::TOO_MANY_REQUESTS               => 'Too Many Requests', // code 429
		StatusCode::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large', // code 431
		StatusCode::UNAVAILABLE_FOR_LEGAL_REASONS   => 'Unavailable For Legal Reasons ', // code 451

		// 5xx
		StatusCode::INTERNAL_SERVER_ERROR           => 'Internal Server Error', // code 500
		StatusCode::NOT_IMPLEMENTED                 => 'Not Implemented', // code 501
		StatusCode::BAD_GATEWAY                     => 'Bad Gateway', // code 502
		StatusCode::SERVICE_UNAVAILABLE             => 'Service Unavailable', // code 503
		StatusCode::GATEWAY_TIMEOUT                 => 'Gateway Timeout', // code 504
		StatusCode::HTTP_VERSION_NOT_SUPPORTED      => 'HTTP Version Not Supported', // code 505
		StatusCode::VARIANT_ALSO_NEGOTIATES         => 'Variant Also Negotiates', // code 506
		StatusCode::INSUFFICIENT_STORAGE            => 'Insufficient Storage', // code 507
		StatusCode::LOOP_DETECTED                   => 'Loop Detected', // code 508
		StatusCode::NOT_EXTENDED                    => 'Not Extended', // code 510
		StatusCode::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required', // code 511
	];

	/**
	 * @param string $code
	 *
	 * @return string
	 *
	 * @throws \Exception
	 */
	public static function getMessageFromCode($code) {
		if (empty(static::$statusCodeMessages[ $code ])) {
			throw new \Exception(sprintf('Could not find message for response code %d', $code));
		}

		return static::$statusCodeMessages[ $code ];
	}
}
