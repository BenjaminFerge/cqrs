<?php

namespace CQRS\Messaging;

use PubSub\PubSubClient;
use PubSub\PubSubServer;

abstract class MessageBus
{
    protected $pubsub;

    public function __construct(PubSubClient $pubsub)
    {
        $this->pubsub = $pubsub;    
    }

    private function _publish(string $topic, string $data)
    {
        echo "publishing: " . $this->getTypePrefix() . ":" . $topic . PHP_EOL;
        $this->pubsub->publish($this->getTypePrefix() . ":" . $topic, $data);
    }

    public function publish(Message $msg)
    {
        $this->_publish($msg->getType(), json_encode($msg->getPayload()));
    }

    abstract public function getTypePrefix(): string;
}