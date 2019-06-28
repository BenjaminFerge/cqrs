<?php

namespace Test;

use EventStore\Event;
use CQRS\Messaging\Command;
use CQRS\AggregateRoot;
use EventStore\DomainEvent;
use Ramsey\Uuid\Uuid;

class User extends AggregateRoot
{
    public $username;
    public $email;

    public function getType(): string
    {
        return "User";
    }

    public function apply(Event $event)
    {
        $payload = $event->getPayload();
        switch ($event->getType())
        {
            case "UserRegistered":
                $username = $payload["username"];
                $this->username = $username;
                printf("User registered! \n");
                break;
            case "UserChangedEmail":
                $email = $payload["email"];
                $this->email = $email;
                printf("User changed email! \n");
                break;
        }
    }
}