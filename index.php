<?php

use CommandString\WebSockets\WebSocket;
use HttpSoft\Response\HtmlResponse;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use React\Socket\SocketServer;
use Router\Http\Router;

require_once "vendor/autoload.php";
require_once "Requests.php";

$loop = Loop::get();

$wsSock = new SocketServer('127.0.0.1:8080', [], $loop);
$webSock = new SocketServer("127.0.0.1:8000", [], $loop);

$router = new Router($webSock, true);

$router->get("/", function ($req, $res) {
    return new HtmlResponse(file_get_contents("public/index.html"));
});

$router->get("/.*(" . \Routes\Files\Files::generateRegex() . ")", [\Routes\Files\Files::class, "main"]);

$socket = new IoServer(new HttpServer(new WsServer(new WebSocket($loop, new Requests))), $wsSock);

$router->getHttpServer()->listen($router->getSocketServer());