<?php
use PubSub\AmqpPubSubClient;
use CQRS\AggregateRepository;
use CQRS\AggregateRoot;
use Ramsey\Uuid\UuidInterface;
use EventStore\Event;
use EventStore\SqliteEventStore;
use CQRS\CommandBus;

require __DIR__ . "/vendor/autoload.php";



class User implements AggregateRoot
{
    public $changes = [];
    public $version = 0;
    public $id;
    public $username;

    public function apply(Event $event)
    {
        $etype = $event->getType();
        $payload = $event->getPayload()();
        switch ($etype)
        {
            case "UserRegistered":
                echo "APPLY USER REGISTERED! \n";
                $this->id = $payload["id"];
                $this->username = $payload["username"];
            break;
            default:
                throw new Exception("Unknown event type: $etype");
        }
    }

    public function getType(): string
    {
        return "User";
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $id): void
    {
        $this->id = $id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getUncommittedChanges(): array
    {
        return $this->changes;
    }

    public function markChangesAsCommitted(): void
    {
        $this->version += count($this->changes);
        $this->changes = [];    
    }
}

$eventStore = new SqliteEventStore();

$repo = new AggregateRepository($eventStore);

$user = new User();
$repo->save($user);
$data = json_encode(["username" => "fee1htv"]);

$exchange = "app";
$client = new AmqpPubSubClient($exchange);

$result = $client->publish("command:RegisterUser", $data);
echo "RESULT:\n";
var_dump($result);