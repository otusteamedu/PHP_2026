<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Task\TaskAggregate;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\ValueObjects\TaskDescription;
use App\Domain\Task\ValueObjects\TaskTitle;
use App\Models\Task;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;

final class EloquentTaskRepository implements TaskRepository
{
    public function save(TaskAggregate $aggregate): Task
    {
        $model = $aggregate->id() !== null
            ? Task::query()->whereKey($aggregate->id())->firstOrFail()
            : new Task;

        $due = $aggregate->dueAt();

        $model->forceFill([
            'user_id' => $aggregate->ownerUserId(),
            'title' => $aggregate->title()->toString(),
            'description' => $aggregate->description()?->toString(),
            'is_done' => $aggregate->isDone(),
            'due_at' => $due !== null ? Carbon::createFromInterface($due) : null,
        ]);

        $model->save();

        if ($aggregate->id() === null) {
            $aggregate->assignPersistedIdentity((int) $model->getKey());
        }

        return $model->fresh();
    }

    public function findForOwner(int $taskId, int $ownerUserId): ?TaskAggregate
    {
        $row = Task::query()->whereKey($taskId)->where('user_id', $ownerUserId)->first();
        if ($row === null) {
            return null;
        }

        return $this->mapFromModel($row);
    }

    private function mapFromModel(Task $row): TaskAggregate
    {
        $due = $row->due_at;
        $immutable = null;
        if ($due instanceof DateTimeInterface) {
            $immutable = DateTimeImmutable::createFromInterface($due);
        }

        return TaskAggregate::restore(
            (int) $row->getKey(),
            (int) $row->user_id,
            TaskTitle::fromString($row->title),
            TaskDescription::fromNullable($row->description),
            (bool) $row->is_done,
            $immutable
        );
    }
}
