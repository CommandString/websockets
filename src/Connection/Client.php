<?php

namespace CommandString\WebSockets\Connection;

use Ratchet\ConnectionInterface;

class Client {
    private array $data = [];
    public function __construct(public readonly ConnectionInterface $connection) {}

    public static function new(ConnectionInterface $connection): self
    {
        return new self($connection);
    }

    public function setData(string $name, mixed $value): self
    {
        $this->data[$name] = $value;
        return $this;
    }
}