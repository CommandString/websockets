<?php

use CommandString\CookieEncryption\Encryption;
use CommandString\Cookies\Cookie;
use CommandString\Pdo\Driver;
use React\Socket\SocketServer;
use Router\Http\Router;
use CommandString\Env\Env;
use WebSocket\Requests;
use WebSocket\WebSocket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Loop;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once __DIR__ . "/vendor/autoload.php";

#  _______ __   _ _   _  _____  ______  _____  __   _ _______ _______ __   _ _______ #
# |______ | \  |  \  /    |   |_____/ |     | | \  | |  |  | |______ | \  |    |     #
# |______ |  \_|   \/   __|__ |    \_ |_____| |  \_| |  |  | |______ |  \_|    |     #

if (!file_exists(__DIR__."/env.json")) {
    file_put_contents(__DIR__."/env.json", file_get_contents(__DIR__."/env.example.json"));
    echo "Environment configuration does not exist, creating env.json!\n";
}

$env = Env::createFromJsonFile(__DIR__."/env.json");
$env->loop = Loop::get();

# _  _  _ _______ ______  _______  _____  _______ _     _ _______ _______ #
# |  |  | |______ |_____] |______ |     | |       |____/  |______    |    #
# |__|__| |______ |_____] ______| |_____| |_____  |    \_ |______    |    #

$websocket = new IoServer(new HttpServer(new WsServer(new WebSocket($env->loop, new Requests))), new SocketServer('127.0.0.1:8080', [], $env->loop));

#  ______  _____  _     _ _______ _______  ______  #
# |_____/ |     | |     |    |    |______ |_____/  #
# |    \_ |_____| |_____|    |    |______ |    \_  #

$router = new Router(new SocketServer("{$env->server->ip}:{$env->server->port}", [], $env->loop), $env->server->dev);

#  ______  _______ _______ _______ ______  _______ _______ _______ #
# |     \ |_____|    |    |_____| |_____] |_____| |______ |______ #
# |_____/ |     |    |    |     | |_____] |     | ______| |______ #

if ($env->database->enabled) {
    $env->driver = Driver::createMySqlDriver($env->database->username, $env->database->password, $env->database->name, $env->database->host, $env->database->port)->connect();
}

#  _______  _____   _____  _     _ _____ _______ _______ #
# |       |     | |     | |____/    |   |______ |______ #
# |_____  |_____| |_____| |    \_ __|__ |______ ______| #

if ($env->cookies->enabled) {
    $env->cookie = new Cookie(new Encryption($env->cookies->encryption_passphrase, $env->cookies->encryption_algo));
}

# _______ _  _  _ _____  ______ #
#    |    |  |  |   |   |  ____ #
#    |    |__|__| __|__ |_____| #

$env->twig = new Environment(new FilesystemLoader(realpath($env->twigConfig->views)));

#  ______  _____  _     _ _______ _______ _______ #
# |_____/ |     | |     |    |    |______ |______ #
# |    \_ |_____| |_____|    |    |______ ______| #
$router
    ->get("/", [Routes\Ws::class, "main"])
    ->get("/.*(".\Routes\Files\Files::generateRegex().")", [\Routes\Files\Files::class, "main"])
    ->map404("/(.*)", [\Routes\ErrorHandler::class, "handle404"])
    ->map500("/(.*)", [\Routes\ErrorHandler::class, "handle500"])
;

$router->getHttpServer()->listen($router->getSocketServer());

echo "HTTP server listening on {$env->server->ip}:{$env->server->port}
WebSocket server listening on {$env->websocket->ip}:{$env->websocket->port}";