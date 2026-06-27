<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

class TelegramChatId
{
    public int $value {
        get {
            return $this->value;
        }
    }

    public function __construct(int $value)
    {
        Assert::integer($value, 'Id чата должно быть числом');
        Assert::notEq($value, 0, 'Id чата должно быть не равно 0');
        $this->value = $value;
    }
}
