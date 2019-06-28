<?php

namespace CQRS\Messaging;

use EventStore\DomainEvent;

class EventFactory
{
    public static function fromMessage($msg)
    {
        $data = json_decode($msg, true);
        return new DomainEvent(
            $data["type"],
            $data["payload"],
            $data["version"],
            $data["id"] ?? null,
            $data["streamId"] ?? null,
            $data["occuredAt"] ?? null,
            $data["recordedAt"] ?? null
        );
    }
}