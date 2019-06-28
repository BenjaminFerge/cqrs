<?php

use PubSub\AmqpPubSubServer;
use EventStore\SqliteEventStore;
use EventStore\Projection;
use EventStore\Event;
use CQRS\CommandHandler;
use PubSub\AmqpPubSubClient;
use EventStore\DomainEvent;
use Ramsey\Uuid\Uuid;

require __DIR__ . "/vendor/autoload.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$exchange = "app";
$server = new AmqpPubSubServer($exchange);
$client = new AmqpPubSubClient($exchange);

$commandHandler = new CommandHandler($server, $client);
$commandHandler->listen("RegisterUser", function($data) {
    echo "Got RegisterUser command!" . PHP_EOL;
    $userId = Uuid::uuid4();
    $e = new DomainEvent("UserRegistered", $data, 1, null, $userId);
    return $e;
});
echo "Starting commandbus...\n";
$commandHandler->start();
echo "CommandBus done.\n";