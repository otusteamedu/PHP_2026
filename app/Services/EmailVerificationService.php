<?php

namespace App\Services;

use App\ObjectValues\Email;

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
        $objectEmail = new Email($email);
        if (!filter_var($objectEmail->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr($objectEmail->getEmail(), strpos($objectEmail->getEmail(), '@') + 1);
        return checkdnsrr($domain, "MX");
    }
}
