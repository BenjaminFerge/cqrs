<?php

namespace CQRS;

use EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;
use EventStore\Event;

interface AggregateRoot extends Entity
{
}