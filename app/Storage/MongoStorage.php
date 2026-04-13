<?php

declare(strict_types=1);

namespace App\Storage;

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

final class MongoStorage implements StorageInterface
{
    use ConditionsTrait;

    private string $ns;

    public function __construct(private readonly Manager $manager, string $db)
    {
        $this->ns = $db . '.events';
    }

    public function addEvent(int $priority, array $conditions, array $event): void
    {
        $bulk = new BulkWrite();
        $bulk->insert(
            [
                'priority' => $priority,
                'conditions' => (object) $conditions,
                'event' => (object) $event
            ],
        );
        $this->manager->executeBulkWrite($this->ns, $bulk);
    }

    public function clearEvents(): void
    {
        $bulk = new BulkWrite();
        $bulk->delete([], ['limit' => 0]);
        $this->manager->executeBulkWrite($this->ns, $bulk);
    }

    public function findBestMatch(array $params): ?array
    {
        $cursor = $this->manager->executeQuery($this->ns, new Query([], ['sort' => ['priority' => -1]]));
        foreach ($cursor as $doc) {
            if ($this->matches((array) $doc->conditions, $params)) {
                return (array) $doc->event;
            }
        }
        return null;
    }
}
