# No Dependency HTTP Server

What it says on the box; a _very very basic_ HTTP server with absolutely zero dependencies.

## Requirements

This library uses the PHP socket library, so please follow the info here: https://www.php.net/manual/en/sockets.installation.php

Most PHP installations already come with this extension.

## Usage

There's 3 main methods:

### HttpServer::infinite

`HttpServer::infinite($callback, $port = 5000, $host = '127.0.0.1', $options = null)` runs indefinitely, until a `false` boolean is returned.

    <?php
    
    use jalsoedesign\NoDependencyHttpServer\HttpServer;
    use jalsoedesign\NoDependencyHttpServer\HttpRequest;
    use jalsoedesign\NoDependencyHttpServer\HttpResponse;
    use jalsoedesign\NoDependencyHttpServer\StatusCode;
    
    require_once(__DIR__ . '/../vendor/autoload.php');
    
    $port = 5000;
    
    printf('Listening on http://localhost:%d/..' . PHP_EOL, $port);
    
    HttpServer::infinite(function(HttpRequest $request) {
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

If you want to exit after something happened (eg. when you receive an OAuth token), you can simply `return false;`:

    <?php
    
    use jalsoedesign\NoDependencyHttpServer\HttpServer;
    use jalsoedesign\NoDependencyHttpServer\HttpRequest;
    use jalsoedesign\NoDependencyHttpServer\HttpResponse;
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
    
        if (!empty($code)) {
            return false; // Exit
        }
    }, $port);
    
    printf('Found code: %s', $code);

### HttpServer::once

`HttpServer::once($callback, $port = 5000, $host = '127.0.0.1', $options = null)` runs only for the first request. After the first request is parsed the server will stop.

    <?php
    
    use jalsoedesign\NoDependencyHttpServer\HttpServer;
    use jalsoedesign\NoDependencyHttpServer\HttpRequest;
    use jalsoedesign\NoDependencyHttpServer\HttpResponse;
    use jalsoedesign\NoDependencyHttpServer\StatusCode;
    
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

### Serve files

If all you want to do is serve a file of folders, simply use `HttpServer::serveFiles($serveFolder, $port = 5000, $host = '127.0.0.1', $options = null)`:

    <?php
    
    use jalsoedesign\NoDependencyHttpServer\HttpServer;
    use jalsoedesign\NoDependencyHttpServer\HttpRequest;
    use jalsoedesign\NoDependencyHttpServer\HttpResponse;
    use jalsoedesign\NoDependencyHttpServer\StatusCode;
    
    require_once(__DIR__ . '/../vendor/autoload.php');
    
    $port = 5000;
    $fileDirectory = realpath(__DIR__ . '/../serve-files'); // This folder is NOT part of the git project
    
    printf('Listening on http://localhost:%d/, serving files from %s..' . PHP_EOL, $port, $fileDirectory);
    
    HttpServer::serveFiles($fileDirectory, $port);

## Options

Options can be supplied as the last argument in `HttpServer::infinite` and `HttpServer::once`, and can customize some internal server stuff if needed:

    <?php
    
    use jalsoedesign\NoDependencyHttpServer\HttpServer;
    use jalsoedesign\NoDependencyHttpServer\HttpRequest;
    use jalsoedesign\NoDependencyHttpServer\HttpResponse;
    use jalsoedesign\NoDependencyHttpServer\HttpServerOptions;
    use jalsoedesign\NoDependencyHttpServer\StatusCode;
    
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
