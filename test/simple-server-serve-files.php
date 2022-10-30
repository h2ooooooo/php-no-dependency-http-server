<?php

use jalsoedesign\NoDependencyHttpServer\HttpServer;

require_once(__DIR__ . '/../vendor/autoload.php');

$port          = 5000;
$fileDirectory = realpath(__DIR__ . '/../serve-files'); // This folder is NOT part of the git project

printf('Listening on http://localhost:%d/, serving files from %s..' . PHP_EOL, $port, $fileDirectory);

HttpServer::serveFiles($fileDirectory, $port);

printf('Server ended' . PHP_EOL);
