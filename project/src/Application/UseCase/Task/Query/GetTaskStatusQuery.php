<?php

declare(strict_types=1);

namespace App\Application\UseCase\Task\Query;

final readonly class GetTaskStatusQuery
{
    public function __construct(public string $taskId)
    {
    }
}
