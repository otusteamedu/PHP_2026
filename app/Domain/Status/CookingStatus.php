<?php

declare(strict_types=1);

namespace App\Domain\Status;

enum CookingStatus: string
{
    case Accepted = 'accepted';
    case Preparing = 'preparing';
    case Cooking = 'cooking';
    case QualityCheck = 'quality_check';
    case Ready = 'ready';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Accepted => 'Заказ принят',
            self::Preparing => 'Подготовка ингредиентов',
            self::Cooking => 'Готовится',
            self::QualityCheck => 'Контроль качества',
            self::Ready => 'Готов к выдаче',
            self::Rejected => 'Отбракован и утилизирован',
        };
    }
}