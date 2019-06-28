<?php

use PubSub\AmqpPubSubServer;
use EventStore\SqliteEventStore;
use EventStore\Projection;
use EventStore\Event;
use CQRS\CommandHandler;
use PubSub\AmqpPubSubClient;
use EventStore\DomainEvent;
use Ramsey\Uuid\Uuid;
use CQRS\Messaging\CommandBus;
use CQRS\Messaging\Command;
use PubSub\TcpPubSubClient;
use PubSub\TcpPubSubMessage;

require __DIR__ . "/vendor/autoload.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$exchange = "app";
$client = new AmqpPubSubClient($exchange);
$data = [
    "username" => "fee1htv"
];
$commandBus = new CommandBus($client);
$registerUser = new Command("RegisterUser", $data);
$commandBus->publish($registerUser);

function cmd_decor(array $payload, string $routing_key)
{
    return array_merge($payload, compact('routing_key'));
}