<?php

namespace CQRS;

use EventStore\Event;

class EventBus extends MessageBus
{
    public function getTypePrefix(): string
    {
        return "event";
    }

    public function publish(Event $e)
    {
        $this->_publish($e->getType(), \json_encode($e->getPayload()));
    }
}