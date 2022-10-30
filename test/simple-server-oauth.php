<?php

use jalsoedesign\NoDependencyHttpServer\packet\HttpRequest;
use jalsoedesign\NoDependencyHttpServer\packet\HttpResponse;
use jalsoedesign\NoDependencyHttpServer\HttpServer;
use jalsoedesign\NoDependencyHttpServer\StatusCode;

require_once(__DIR__ . '/../vendor/autoload.php');

$port = 5000;

printf('Listening on http://localhost:%d/..' . PHP_EOL, $port);

$code = null;

HttpServer::infinite(function(HttpRequest $request) use (&$code) {
	$requestUrl = $request->getRequestUrl();
	parse_str(urldecode(parse_url($requestUrl, PHP_URL_QUERY)), $query);

	if ( ! empty($query['code'])) {
		$code = $query['code'];

		$html = sprintf('Code: %s', $code);
	} else {
		$code = null;

		$html = 'No "code" was found in query';
	}

	// Make sure we use sendResponse() directly, so that we can return FALSE to exit the loop
	$request->sendResponse(new HttpResponse(StatusCode::OK, $html, ['Content-Type' => 'text/html']));

	if ( ! empty($code)) {
		return false; // Exit
	}
}, $port);

printf('Found code: %s', $code);
