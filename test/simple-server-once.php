<?php

use jalsoedesign\NoDependencyHttpServer\packet\HttpRequest;
use jalsoedesign\NoDependencyHttpServer\packet\HttpResponse;
use jalsoedesign\NoDependencyHttpServer\HttpServer;
use jalsoedesign\NoDependencyHttpServer\statusCode\StatusCode;

require_once(__DIR__ . '/../vendor/autoload.php');

$port = 5000;

printf('Listening on http://localhost:%d/..' . PHP_EOL, $port);

HttpServer::once(function(HttpRequest $request) {
	$body = $request->getBody();

	$htmlLines = [
		sprintf('Body: <strong>%s</strong>', htmlentities($body)),
		sprintf('Request method: <strong>%s</strong>', htmlentities($request->getRequestMethod())),
		sprintf('Request URL: <strong>%s</strong>', htmlentities($request->getRequestUrl())),
		sprintf('HTTP version: <strong>%s</strong>', htmlentities($request->getHttpVersion())),
	];

	$html = implode('<br />' . PHP_EOL, $htmlLines);

	return new HttpResponse(StatusCode::OK, $html, ['Content-Type' => 'text/html']);
}, $port);

printf('Server ended' . PHP_EOL);
