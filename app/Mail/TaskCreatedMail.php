<?php

namespace App\Mail;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Task $task) {}

    public function build(): self
    {
        $task = $this->task;

        $body = 'Задача #'.$task->id.': '.$task->title.PHP_EOL;
        if ($task->description) {
            $body .= PHP_EOL.$task->description;
        }

        return $this->subject('Новая задача: '.$task->title)
            ->text('mail.raw')
            ->with(['content' => $body]);
    }
}

