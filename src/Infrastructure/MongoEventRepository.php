<?php
declare(strict_types=1);

namespace Evgeny87\RedisLab\Infrastructure;

use Evgeny87\RedisLab\Contract\EventRepositoryInterface;
use Evgeny87\RedisLab\Domain\EventDto;
use MongoDB\Client;

final class MongoEventRepository implements EventRepositoryInterface
{
    private $collection;

    public function __construct(Client $client)
    {
	$this->collection = $client->selectCollection('app', 'events');
	try {
	    $this->collection->createIndexes([
		['key' => ['priority' => -1], 'name' => 'priority_desc'],
		['key' => ['conditions' => 1], 'name' => 'conditions_asc']
	    ]);
	} catch (\Exception $e) {
	    // индексы уже существуют или база ещё не готова – игнорируем
	}
    }

    public function store(EventDto $event): string
    {
        $result = $this->collection->insertOne([
            'priority'   => $event->priority,
            'conditions' => $event->conditions,
            'payload'    => $event->payload
        ]);
        return (string)$result->getInsertedId();
    }

    public function findCandidates(array $criteria): array
    {
	$cursor = $this->collection->find();
	$results = [];

	foreach ($cursor as $doc) {
	    $match = true;
	    foreach ($doc['conditions'] as $k => $v) {
		if (!isset($criteria[$k]) || $criteria[$k] != $v) {
		$match = false;
		break;
	        }
	    }
	    if ($match) {
	        $results[] = [
		    'priority' => $doc['priority'],
		    'payload'  => (array)$doc['payload']
	        ];
	    }
	}
	return $results;
    }

    public function clearAll(): void
    {
        $this->collection->deleteMany([]);
    }
}
