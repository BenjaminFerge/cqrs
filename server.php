<?php

use PubSub\AmqpPubSubServer;
use EventStore\SqliteEventStore;
use EventStore\Projector;
use EventStore\Projection;
use EventStore\Event;
use EventStore\LibeventEventStoreListener;
use EventStore\EventStoreService;
use EventStore\DomainEvent;
use CQRS\CommandBus;
use CQRS\AggregateRepository;
use CQRS\Aggregate;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;
use CQRS\EventBus;

require __DIR__ . "/vendor/autoload.php";

$exchange = "app";
$server = new AmqpPubSubServer($exchange);


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

// function registerUser($eventStore, $userId)
// {
//     $userProjection = new UserProjection();
//     $userStream = $eventStore->createStream("User");    
//     $projector = new Projector($userProjection, $userStream, null, 0, Projector::VERBOSE);
//     $eventStore->addProjector($projector);
//     $userRegistered = new DomainEvent("UserRegistered", compact("userId"), 1);
//     $eventStore->push($userStream->getId(), $userRegistered);
//     return $userRegistered;
// }

// $server->subscribe("command:register-user", function($msg) use ($eventStore) {
//     $data = json_decode($msg->getBody(), true);
//     echo "register-user!\n";
//     registerUser($eventStore, $data["userId"]);
// });

$eventBus = new EventBus($server);
$eventBus->subscribe("UserRegistered", function() {
    echo "USER REGISTERED!\n";
    return "returned from user registered\n";
});
$commandBus = new CommandBus($server);
$registerUserHandler = new RegisterUserHandler($eventBus);
$commandBus->subscribe("RegisterUser", $registerUserHandler);
// $commandBus->subscribe("RegisterUser", "registerUserHandler");
// $commandBus->pipe("RegisterUser", "registerUserHandler", $eventBus);

$server->start();

function _registerUserHandler($msg)
{
    echo "fn() REGISTER USER HANDLER\n";
    $payload = json_decode($msg->getBody(), true);
    $event = new DomainEvent("UserRegistered", $payload, 1);
    return $event->toJson();
}

class RegisterUserHandler
{
    public $eventBus;
    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }
    public function __invoke($msg)
    {
        echo "REGISTER USER HANDLER\n";
        $payload = json_decode($msg->getBody(), true);
        $event = new DomainEvent("UserRegistered", $payload, 1);
        $this->eventBus->publish($event);
        return "RPC-t ne felejtsem el opcionálissá tenni pl egy WAIT_FOR_RESPONSE flaggel... az rpc nem pubsub";
    }
}