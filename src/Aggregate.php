<?php

namespace CQRS;

use EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;
use EventStore\Event;

interface Aggregate
{
    public function handle(Command $cmd);
    public function apply(Event $e);
}