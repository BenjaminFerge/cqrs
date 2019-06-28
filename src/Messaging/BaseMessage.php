<?php

namespace CQRS\Messaging;

abstract class BaseMessage implements Message
{
    private $type;
    private $payload;

    public function __construct($type, $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function getPayload() : array
    {
        return $this->payload;
    }
}