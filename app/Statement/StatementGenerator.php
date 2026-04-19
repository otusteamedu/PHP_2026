<?php

declare(strict_types=1);

namespace App\Statement;

final class StatementGenerator
{
    public function generate(StatementRequest $request): string
    {
        return <<<TEXT
        Банковская выписка
        ------------------------------------
        Запрос:    {$request->id}
        Счёт:      {$request->account}
        Период:    с {$request->dateFrom} по {$request->dateTo}
        ------------------------------------
        Операций за период: 1234
        Итоговый баланс:    1234.56 RUB
        ------------------------------------
        Выписка сформирована.
        TEXT;
    }
}