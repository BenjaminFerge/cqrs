<?php

namespace CQRS;

use EventStore\Event;

interface EventHandler
{
    public function handle(Event $e): void;
}