<?php

declare(strict_types=1);

namespace App\Enum;

enum StatementStatus: string
{
    case New = 'new';
    case Processing = 'processing';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Новый',
            self::Processing => 'В обработке',
            self::Completed => 'Выполнен',
        };
    }

    public static function tryFromString(string $value): ?self
    {
        return self::tryFrom($value);
    }
}
