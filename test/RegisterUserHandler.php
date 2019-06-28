<?php

namespace Test;

use CQRS\CommandHandler;
use EventStore\DomainEvent;
use EventStore\Event;

class RegisterUserHandler extends CommandHandler
{
    public function handle(RegisterUser $cmd): Event
    {
        return new DomainEvent("UserRegistered", $cmd->getPayload(), 1);
    }
}