<?php

namespace CQRS\Messaging;

class CommandBus extends MessageBus
{
    public function getTypePrefix(): string
    {
        return "command";
    }
}