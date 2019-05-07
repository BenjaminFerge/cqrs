<?php

namespace CQRS\Messaging;

interface Message
{
    public function getType(): string;
    public function getPayload(): array;
}