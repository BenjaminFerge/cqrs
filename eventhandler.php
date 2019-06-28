<?php

use PubSub\AmqpPubSubServer;
use EventStore\SqliteEventStore;
use EventStore\Projection;
use EventStore\Event;
use CQRS\CommandHandler;
use CQRS\Messaging\EventBus;
use CQRS\EventHandler;
use EventStore\Projector;

require __DIR__ . "/vendor/autoload.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$exchange = "app";
$server = new AmqpPubSubServer($exchange);


$eventStore = new SqliteEventStore();
$eventHandler = new EventHandler($server, $eventStore);
$eventHandler->listen("UserRegistered");
// $userProjection = new UserProjection();
echo "Starting server...\n";
$eventHandler->start();
echo "Server done.\n";

class UserProjection implements Projection
{
    public function handle($state, Event $e)
    {
        $payload = $e->getPayload();
        switch ($e->getType())
        {
            case "UserRegistered":
                echo "YAY USER REGISTERED!!!!\n";
                // file_put_contents("user1.txt", json_encode($payload, JSON_PRETTY_PRINT));
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
