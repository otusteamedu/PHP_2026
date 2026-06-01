<?php

declare(strict_types=1);

namespace App\Service;

final class EmailValidator
{
    public function isValid(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);

        if ($domain === false || !checkdnsrr($domain)) {
            return false;
        }

        return true;
    }

    public function filterValid(array $emails): array
    {
        return array_filter($emails, fn($email) => $this->isValid($email));
    }
}
