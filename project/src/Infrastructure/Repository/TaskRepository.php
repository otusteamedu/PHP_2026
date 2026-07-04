<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use App\Domain\Enum\Status;
use App\Domain\Repository\TaskRepositoryInterface;
use App\Domain\ValueObject\Id;
use Predis\Client;

final class TaskRepository implements TaskRepositoryInterface
{
    private const string KEY_PREFIX = 'task:';

    public function __construct(
        private readonly Client $redis
    ) {
    }

    public function find(Id $id): ?Task
    {
        /** @var array<string, int|string> $data */
        $data = $this->redis->hgetall($this->key($id));

        if (empty($data)) {
            return null;
        }

        return new Task($id, Status::from($data['status']));
    }

    public function save(Task $task): void
    {
        $this->redis->hset(
            $this->key($task->id),
            'status',
            $task->getStatus()->value
        );
    }

    private function key(Id $id): string
    {
        return self::KEY_PREFIX . $id->value;
    }
}
