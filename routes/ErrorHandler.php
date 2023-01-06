<?php

namespace Routes;

use CommandString\Env\Env;
use HttpSoft\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorHandler {
    public static function handle404(RequestInterface $req): ResponseInterface
    {
        return new HtmlResponse(Env::get()->twig->render("errors/404.html", [
            "uri" => $req->getRequestTarget()
        ]), 404);
    }

    public static function handle500(): ResponseInterface
    {
        return new HtmlResponse(Env::get()->twig->render("errors/404.html", [
            "uri" => $_SERVER["REQUEST_URI"]
        ]), 500);
    }
}