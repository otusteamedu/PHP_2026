<?php

declare(strict_types=1);

namespace App\Notification;

use App\Statement\StatementRequest;

interface Notifier
{
    public function notify(StatementRequest $request, string $statement): void;
}
