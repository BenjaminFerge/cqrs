<?php

namespace CQRS\Messaging;

use EventStore\Event;
use PubSub\PubSubServer;
use EventStore\EventStore;
use EventStore\DomainEvent;

class EventBus extends MessageBus
{
    public function getTypePrefix(): string
    {
        return "event";
    }
}