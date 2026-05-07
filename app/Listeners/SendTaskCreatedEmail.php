<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Mail\TaskCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTaskCreatedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public int $tries = 5;

    public array $backoff = [10, 30, 90, 180, 300];

    public function handle(TaskCreated $event): void
    {
        $task = $event->task;
        $task->loadMissing('user');
        $email = $task->user?->email;
        if ($email === null || $email === '') {
            return;
        }

        try {
            Mail::to($email)->send(new TaskCreatedMail($task));
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }
}
