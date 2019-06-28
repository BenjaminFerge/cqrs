<?php

namespace CQRS;

use Ramsey\Uuid\UuidInterface;
use EventStore\Event;
use CQRS\Messaging\Command;

abstract class AggregateRoot
{
    private $id;
    private $changes = [];
    private $version = 0;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUncommittedChanges(): array
    {
        return $this->changes;
    }

    public function markChangesAsCommitted(): void
    {
        $this->version += count($this->changes);
        $this->changes = [];    
    }

    public function occur(Event $e) : void
    {
        $this->changes[] = $e;
    }

    public function getType() : string
    {
        return \get_called_class();
    }

    abstract public function apply(Event $event);
    abstract public function handle(Command $cmd) : Event;
}