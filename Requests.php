<?php

use CommandString\WebSockets\Message;

class Requests extends \CommandString\WebSockets\Requests {
    public static function test(Message $request, string $name) {
        $request->setResponseData("name", $name);
        $request->respond();
    }
}