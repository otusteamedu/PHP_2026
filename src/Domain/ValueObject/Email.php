<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Email
{
    public string $value {
        get {
            return $this->value;
        }
        set {
            $this->value = $value;
        }
    }

    public function __construct(string $email)
    {
        Assert::email($email, 'Передан неправильный email');
        $this->value = $email;
    }
}
