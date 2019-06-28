<?php

namespace CQRS;

use EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;
use CQRS\Messaging\EventBus;

class AggregateRepository
{
    protected $eventStore;
    protected $eventBus;

    public function __construct(EventStore $eventStore, EventBus $eventBus)
    {
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    public function save(AggregateRoot $aggr)
    {
        if ($aggr->getVersion() === 0)
        {
            if ($aggr->getId())
            {
                throw new \Exception("AggregateRoot's ID should be empty before the first event.");
            }
            $stream = $this->eventStore->createStream($aggr->getType());
            $aggr->setId($stream->getId());
        } else {
            $stream = $this->eventStore->getStream($aggr->getId());
        }

        foreach ($aggr->getUncommittedChanges() as $e)
        {
            $this->eventStore->push($aggr->getId(), $e);
            $this->eventBus->publish($e);
        }
        $aggr->markChangesAsCommitted();
    }

    public function get(UuidInterface $id = null)
    {
        
    }
}