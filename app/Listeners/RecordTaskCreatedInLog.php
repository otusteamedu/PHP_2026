<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class RecordTaskCreatedInLog implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public function handle(TaskCreated $event): void
    {
        Log::channel('task_events')->info('task_created', [
            'task_id' => $event->task->id,
            'user_id' => $event->task->user_id,
            'title' => $event->task->title,
        ]);
    }
}
