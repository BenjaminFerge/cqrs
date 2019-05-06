<?php

namespace CQRS;

abstract class BaseMessage
{
    private $type;
    private $payload;

    public function __construct($type, $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getPayload()
    {
        return $this->payload;
    }
}