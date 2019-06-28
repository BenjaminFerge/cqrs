<?php
use PubSub\AmqpPubSubClient;
use CQRS\AggregateRepository;
use CQRS\AggregateRoot;
use Ramsey\Uuid\UuidInterface;
use EventStore\Event;
use EventStore\SqliteEventStore;
use CQRS\CommandBus;

require __DIR__ . "/vendor/autoload.php";

$data = json_encode(["username" => "fee1htv"]);
$exchange = "app";
$client = new AmqpPubSubClient($exchange);

$result = $client->publish("RegisterUser", $data);
echo "RESULT:\n";
var_dump($result);
return;


$commandBus = new CommandBus($server);
$registerUserHandler = new RegisterUserHandler($eventBus);
$commandBus->subscribe("RegisterUser", $registerUserHandler);
// $commandBus->subscribe("RegisterUser", "registerUserHandler");
// $commandBus->pipe("RegisterUser", "registerUserHandler", $eventBus);