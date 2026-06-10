<?php

declare(strict_types=1);

namespace App\Notification;

use App\Statement\StatementRequest;

final class ConsoleNotifier implements Notifier
{
    public function notify(StatementRequest $request, string $statement): void
    {
        echo PHP_EOL;
        echo '==================== ГОТОВО ====================' . PHP_EOL;
        echo "Запрос {$request->id} обработан, email: {$request->email}" . PHP_EOL;
        echo $statement . PHP_EOL;
        echo '===============================================' . PHP_EOL;
    }
}
