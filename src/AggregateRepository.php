<?php

namespace CQRS;

use EventStore\EventStore;
use Ramsey\Uuid\UuidInterface;

class AggregateRepository
{
    protected $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function save(Aggregate $aggr)
    {
        if ($aggr->getVersion() === 0)
        {
            if ($aggr->getId())
            {
                throw new \Exception("Aggregate's ID should be empty before the first event.");
            }
            $stream = $this->eventStore->createStream($aggr->getType());
            $aggr->setId($stream->getId());
        } else {
            $stream = $this->eventStore->getStream($aggr->getId());
        }

        foreach ($aggr->getUncommittedChanges() as $e)
        {
            $this->eventStore->push($aggr->getId(), $e);
        }
        $aggr->markChangesAsCommitted();
    }
}