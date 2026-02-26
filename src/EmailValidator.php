<?php

declare(strict_types=1);

namespace App;
class EmailValidator
{
    public function validate(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/';
        if (!preg_match($pattern, $email, $matches)) {
            return false;
        }

        $domain = $matches[1];
        if (!checkdnsrr($domain)) {
            return false;
        }
        return true;
    }
}
