<?php

namespace App\Services;

class EmailVerificationService
{
    public function checkEmailList(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            $results[] = [
                'email' => $email,
                'is_valid' => $this->checkEmail($email),
            ];
        }
        return $results;
    }

    public function checkEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr($email, strpos($email, '@') + 1);
        return checkdnsrr($domain, "MX");
    }
}
