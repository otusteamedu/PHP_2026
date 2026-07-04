<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum Status: string
{
    case Created = 'created';
    case InProgress  = 'inprogress';
    case Cancelled  = 'cancelled';
    case Completed  = 'completed';

    public function title(): string
    {
        return match ($this) {
            self::Created => 'Создана',
            self::InProgress => 'В работе',
            self::Cancelled => 'Отменена',
            self::Completed => 'Выполнена',
        };
    }
}
