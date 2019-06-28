<?php

namespace CQRS;

use CQRS\Messaging\Command;
use EventStore\Event;
use CQRS\Messaging\EventBus;
use CQRS\Messaging\MessageBus;
use CQRS\Messaging\Message;
use PhpAmqpLib\Message\AMQPMessage;
use PubSub\PubSubServer;
use PubSub\PubSubClient;

class CommandHandler extends MessageHandler
{
    private $server;
    private $client;
    private $handlers = [];

    public function __construct(PubSubServer $server, PubSubClient $client)
    {
        $this->server = $server;
        $this->client = $client;
    }

    public function handle(Command $cmd) : void
    {
        $e = $this->handlers[$cmd->getType()]($cmd->getPayload());
        if (!($e instanceof Event)) {
            throw new \TypeError("CommandHandler must return with Event object");
        }
        echo "Occured: " . $e->getType() . PHP_EOL;
        $this->client->publish($this->getTypePrefix() . ':' . $e->getType(), $e->toJson());
    }

    public function listen(string $topic, callable $handler) : void
    {
        $topic = $this->getTypePrefix() . ':' . $topic;
        $this->handlers[$topic] = $handler;
        $this->server->subscribe($topic, $this);
    }

    public function __invoke($msg) : void
    {
        echo "CommandHandler:__invoke" . PHP_EOL;
        if ($msg instanceof AMQPMessage) {
            $type = $msg->delivery_info["routing_key"];
            $data = json_decode($msg->getBody(), true);
            $command = new Command($type, $data);
            $this->handle($command);
        }

    }

    public function getTypePrefix(): string
    {
        return 'command';
    }
}