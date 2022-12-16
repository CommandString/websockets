<?php

use CommandString\WebSockets\Message;

class Requests extends \CommandString\WebSockets\Requests {
    public static function test(Message $request, string $name) {
        if (empty($name)) {
            $request->addError("The name supplied must not be empty!")->respond();
            return;
        }

        $request->setResponseData("text", "Your name is $name")->respond();
    }
}