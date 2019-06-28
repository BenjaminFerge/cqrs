<?php

namespace CQRS;

use PhpAmqpLib\Message\AMQPMessage;
use PubSub\PubSubServer;
use EventStore\EventStore;
use EventStore\Event;
use EventStore\Projector;
use EventStore\DomainEvent;
use Ramsey\Uuid\Uuid;

class EventHandler extends MessageHandler
{
    private $server;
    private $eventStore;

    public function __construct(PubSubServer $server, EventStore $eventStore)
    {
        $this->server = $server;
        $this->eventStore = $eventStore;
    }

    public function handle(Event $e) : void
    {
        echo "handling Event!\n";
        $this->eventStore->push($e->getStreamId(), $e);
    }

    public function listen(string $topic) : void
    {
        $topic = $this->getTypePrefix() . ':' . $topic;
        echo "Listening to: " . $topic . PHP_EOL;
        $this->server->subscribe($topic, $this);
    }

    public function addProjector(Projector $projector)
    {
        $this->eventStore->addProjector($projector);
    }

    public function __invoke($msg) : void
    {
        echo "EventHandler:__invoke" . PHP_EOL;
        if ($msg instanceof AMQPMessage) {
            $type = $msg->delivery_info["routing_key"];
            $data = json_decode($msg->getBody(), true);
            $e = new DomainEvent(
                $data["type"],
                $data["payload"],
                $data["version"],
                Uuid::fromString($data["id"]),
                Uuid::fromString($data["streamId"]),
                $data["occuredAt"],
                $data["recordedAt"]
            );
            $this->handle($e);
        }

    }
    
    public function getTypePrefix(): string
    {
        return 'event';
    }
}