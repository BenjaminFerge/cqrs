<?php

namespace CQRS;

use CQRS\Messaging\Command;
use EventStore\Event;
use EventStore\EventStore;

abstract class Aggregate
{
    private $root;
    private $objs = [];
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }
    
    public function setRoot(AggregateRoot $root)
    {
        $this->root = $root;
    }

    public function addObject($obj)
    {
        $this->objs[] = $obj;
    }
    
    public function getRoot() : AggregateRoot
    {
        return $this->root;
    }
    
    public function load()
    {

    }

    abstract public function getAggregateType() : string;

    abstract public function handle(Command $cmd) : Event;
}