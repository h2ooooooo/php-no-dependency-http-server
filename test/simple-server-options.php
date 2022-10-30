<?php

use jalsoedesign\NoDependencyHttpServer\packet\HttpRequest;
use jalsoedesign\NoDependencyHttpServer\packet\HttpResponse;
use jalsoedesign\NoDependencyHttpServer\HttpServer;
use jalsoedesign\NoDependencyHttpServer\options\HttpServerOptions;
use jalsoedesign\NoDependencyHttpServer\statusCode\StatusCode;

require_once(__DIR__ . '/../vendor/autoload.php');

$port = 5000;

printf('Listening on http://localhost:%d/..' . PHP_EOL, $port);

$httpServerOptions = HttpServerOptions::build()
                                      ->setIncomingClientsBacklog(5)
                                      ->setUsleepTime(300)
                                      ->setReadBufferSize(1024);

HttpServer::once(function(HttpRequest $request) {
	return new HttpResponse(StatusCode::OK, 'Hello', ['Content-Type' => 'text/html']);
}, $port, '127.0.0.1', $httpServerOptions);

printf('Server ended' . PHP_EOL);
