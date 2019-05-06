<?php

namespace CQRS;

use PubSub\PubSubServer;

class Router
{
    protected $routes;

    public function __construct(PubSubServer $pubsub)
    {
        $this->pubsub = $pubsub;    
    }

    public function subscribe(string $topic, callable $callback)
    {
        $this->pubsub->subscribe($this->getTypePrefix() . ":" . $topic, $callback);
    }

    protected function _publish(string $topic, string $data)
    {
        $this->pubsub->publish($this->getTypePrefix() . ":" . $topic, $data);
    }

    abstract public function getTypePrefix(): string;
}