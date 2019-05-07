<?php

namespace CQRS\Messaging;

class CommandBus extends MessageBus
{
    public function getTypePrefix(): string
    {
        return "command";
    }

    public function publish(Command $cmd)
    {
        $this->_publish($cmd->getType(), json_ecnode($cmd->getPayload()));
    }
}