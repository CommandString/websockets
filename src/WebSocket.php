<?php

namespace CommandString\WebSockets;

use CommandString\WebSockets\Connection\Client;
use CommandString\WebSockets\Connection\Clients;
use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class WebSocket implements MessageComponentInterface {
    private Clients $clients;

    public function __construct(private Requests $requests)
    {
        $this->clients = new Clients;
    }
    
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->addClient(Client::new($conn));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        if ($json_msg = json_decode($msg)) {
            $request = Message::new($this->clients->getClient($from), $json_msg);

            if (!isset($json_msg->endpoint) || empty($json_msg->endpoint)) {
                $request->addError("You must supply an endpoint with the request!")->respond();
                return;
            }

            $this->requests::handle($request);
        }
    }

    public function onClose(ConnectionInterface $connection)
    {
        $this->clients->detachClient($connection);
    }

   public function onError(ConnectionInterface $conn, Exception $e)
   {
    
   }    
}