<?php

namespace Test;

use EventStore\DomainEvent;
use CQRS\Messaging\Command;

class RegisterUser extends Command
{
    public function __constructor(array $payload)
    {
        parent::__constructor("RegisterUser", $payload);
    }
}