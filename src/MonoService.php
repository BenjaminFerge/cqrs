<?php

namespace CQRS;

use PhpAmqpLib\Message\AMQPMessage;
use PubSub\PubSubServer;
use Ramsey\Uuid\Uuid;
use CQRS\Messaging\Command;

class MonoService
{
    private $handlers = [];
    private $server;

    public function __construct(PubSubServer $server)
    {
        $this->server = $server;
    }

    public function attachHandler(MessageHandler $handler)
    {
        if (!isset($this->handlers[$handler->getTypePrefix()])) {
            $this->handlers[$handler->getTypePrefix()] = [];
        }
        $this->handlers[$handler->getTypePrefix()][\get_class($handler)] = $handler;
        echo "Attached handler (" . count($this->handlers) . "): " . get_class($handler) . PHP_EOL;
    }

    private function listen(string $prefix, string $topic, callable $handler) : void
    {
        $topic = "$prefix:$topic";
        $this->handlers[$topic] = $handler;
        $this->server->subscribe($topic, $this);
    }

    public function __invoke($msg) : void
    {
        echo "INVOKE" . PHP_EOL;
        if ($msg instanceof AMQPMessage) {
            $type = $msg->delivery_info["routing_key"];
            var_dump($type);return;
            $data = json_decode($msg->getBody(), true);
            $command = new Command($type, $data);
            $this->handle($command);
        }

    }

    public function start() : void
    {
        $this->server->start();
    }
}