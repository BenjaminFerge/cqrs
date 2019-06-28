<?php

use PubSub\AmqpPubSubServer;
use EventStore\SqliteEventStore;
use EventStore\Projection;
use EventStore\Event;
use CQRS\CommandHandler;
use PubSub\AmqpPubSubClient;
use EventStore\DomainEvent;
use Ramsey\Uuid\Uuid;

use CQRS\Messaging\EventBus;
use CQRS\EventHandler;
use EventStore\Projector;
use CQRS\MonoService;
use PubSub\TcpPubSubServer;
use PubSub\TcpPubSubClient;

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


class UserProjection implements Projection
{
    public function handle($state, Event $e)
    {
        $payload = $e->getPayload();
        switch ($e->getType())
        {
            case "UserRegistered":
                echo "YAY USER REGISTERED!!!!\n";
                file_put_contents(__DIR__ . "/user1.txt", json_encode($payload, JSON_PRETTY_PRINT));
                // return $payload;
            break;
            case "UserChangedEmail":
                echo "changed email!!!!\n";
                $state["email"] = $payload["email"];

                // file_put_contents("user2.txt", json_encode($state, JSON_PRETTY_PRINT));
                // return $state;
            break;
            default:
                throw new Exception("ismeretlen esemény: " . $e->getType());
        }
    }
}

$userProjection = new UserProjection();

$eventStore = new SqliteEventStore(__DIR__ . '/db.sqlite');
$userStream = $eventStore->createStream("User");
$projector = new Projector($userProjection, $userStream, null, 0, Projector::VERBOSE);
$eventStore->addProjector($projector);
$eventHandler = new EventHandler($server, $eventStore);
$eventHandler->listen("UserRegistered");

$monoService = new MonoService($server);
$monoService->attachHandler($commandHandler);
$monoService->attachHandler($eventHandler);
$monoService->start();
