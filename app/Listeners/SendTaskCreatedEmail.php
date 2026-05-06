<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTaskCreatedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public function handle(TaskCreated $event): void
    {
        $task = $event->task;
        $task->loadMissing('user');
        $email = $task->user?->email;
        if ($email === null || $email === '') {
            return;
        }

        $body = 'Задача #'.$task->id.': '.$task->title.PHP_EOL;
        if ($task->description) {
            $body .= PHP_EOL.$task->description;
        }

        Mail::raw($body, function ($message) use ($email, $task): void {
            $message->to($email)->subject('Новая задача: '.$task->title);
        });
    }
}
