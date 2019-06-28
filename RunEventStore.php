<?php
use EventStore\SqliteEventStore;
use EventStore\Projector;
use EventStore\Projection;
use EventStore\Event;
use EventStore\LibeventEventStoreListener;
use EventStore\EventStoreService;

require "vendor/autoload.php";
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
$eventStore = new SqliteEventStore();
$userProjection = new UserProjection();
$userStream = $eventStore->createStream("User");
$projector = new Projector($userProjection, $userStream, null, 0, Projector::VERBOSE);
$eventStore->addProjector($projector);

$port = require "testport.php";
$listener = new LibeventEventStoreListener($eventStore, $port);
$service = new EventStoreService($listener);
$service->run();
