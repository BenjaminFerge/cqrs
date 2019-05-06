<?php

namespace CQRS;

interface Message
{
    public function getType(): string;
    public function getPayload(): array;
}