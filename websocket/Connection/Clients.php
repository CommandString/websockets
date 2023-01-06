<?php

namespace WebSocket\Connection;

use Ratchet\ConnectionInterface;

class Clients {
    private array $clients = [];

    public function addClient(Client $client): self
    {
        $this->clients[$client->connection->resourceId] = $client;
        return $this;
    }

    public function getClient(ConnectionInterface $connection): ?Client
    {
        return $this->clients[$connection->resourceId] ?? null;
    }

    public function detachClient(ConnectionInterface $connection) {
        $this->getClient($connection)->connection->close();
        unset($this->clients[$connection->resourceId]);
    }
}