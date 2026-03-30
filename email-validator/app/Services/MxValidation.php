<?php

declare(strict_types=1);

namespace App\Services;

class MxValidation implements EmailValidationInterface
{
    public function check(string $email): ValidationResult
    {
        $domain = substr(strrchr($email, "@"), 1);

        if (!$domain) {
            return new ValidationResult(false, 'Ошибка в адресе электронной почты');
        }

        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return new ValidationResult(false, 'Ошибка доменного имени у электронной почты');
        }

        $hasMx = checkdnsrr($domain) || !empty(@dns_get_record($domain, DNS_MX));

        return $hasMx
            ? new ValidationResult(true)
            : new ValidationResult(false, 'Ошибка в DNS записи у адреса электронной почты');

    }
}