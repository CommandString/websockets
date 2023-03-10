<?php

namespace CommandString\WebSockets;

abstract class Requests {
    public static function handle(Message $request) {
        $name = $request->getRequest()->endpoint ?? "N/A";

        $reflectionClass = new \ReflectionClass(get_called_class());

        foreach ($reflectionClass->getMethods() as $currentMethod) {
            if ($currentMethod->name == "handle" || $currentMethod->name !== $name) {
                continue;
            }

            $method = $currentMethod;
            break;
        }

        if (!isset($method)) {
            $request->addError("Endpoint $name does not exist");
            $request->respond();
            return;
        }

        $parameters = [];

        foreach ($method->getParameters() as $parameter) {
            if ($parameter->name === "request") {
                $parameters[] = $request;
                continue;
            }

            if (isset($request->getRequest()->{$parameter->name})) {
                $parameters[] = $request->getRequest()->{$parameter->name};
            } else {
                $request->addError("You must supply $parameter->name to the $name endpoint!");
                $request->respond();
                return;
            }
        }

        get_called_class()::$name(...$parameters);
    }
}