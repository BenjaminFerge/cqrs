<?php

namespace CQRS;

use EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;
use EventStore\Event;

interface Aggregate
{
    public function getUncommittedChanges(): array;
    public function markChangesAsCommitted(): void;
    public function getId(): ?UuidInterface;
    public function setId(UuidInterface $id): void;
    public function getType(): string;
    public function getVersion(): int;
    public function apply(Event $event);
}