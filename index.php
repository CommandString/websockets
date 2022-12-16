<?php

use CommandString\WebSockets\WebSocket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

require_once "vendor/autoload.php";
require_once "Requests.php";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new WebSocket(new Requests)
        )
    ),
    8080
);

$server->run();