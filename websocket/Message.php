<?php

namespace WebSocket;

use WebSocket\Connection\Client;
use stdClass;

class Message {
    private stdClass $response;
    private array $errors = [];

    public function __construct(private Client $client, private stdClass|string $request)
    {
        $this->response = new stdClass;
    }

    public static function new(Client $client, stdClass|string $request): self
    {
        return new self($client, $request);
    }

    public function setResponseData(string $key, mixed $value): self
    {
        $this->response->$key = $value;
        return $this;
    }

    public function getRequest(): stdClass
    {
        return $this->request;
    }

    public function addError(string $message): self
    {
        $this->errors[] = $message;
        return $this;
    }

    public function __toString(): string
    {
        return json_encode([
            "response" => $this->response,
            "request" => $this->request,
            "errors" => $this->errors
        ]);
    }

    public function respond() {
        $this->client->connection->send($this);
    }
}