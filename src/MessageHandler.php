<?php

namespace CQRS;

abstract class MessageHandler
{
    abstract public function __invoke($msg) : void;
    abstract public function getTypePrefix(): string;

    public function start() : void
    {
        $this->server->start();
    }
}