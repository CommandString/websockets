<?php

namespace Routes;

use CommandString\Env\Env;
use HttpSoft\Response\HtmlResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Ws {
    public static function main(RequestInterface $req, ResponseInterface $res): ResponseInterface
    {
        return new HtmlResponse(Env::get()->twig->render("Ws.html"));
    }
}