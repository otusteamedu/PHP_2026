<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use App\Domain\EmailValidationRuleInterface;
use App\Domain\ValidationResult;
use App\Domain\ValidationMessages;

class MxValidation implements EmailValidationRuleInterface
{
    public function check(string $email): ValidationResult
    {
        $domain = substr(strrchr($email, "@"), 1);

        if (!$domain) {
            return new ValidationResult(false, ValidationMessages::UNKNOWN_ERROR);
        }

        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return new ValidationResult(false, ValidationMessages::INVALID_DOMAIN);
        }

        $hasMx = checkdnsrr($domain) || !empty(@dns_get_record($domain, DNS_MX));

        return $hasMx
            ? new ValidationResult(true)
            : new ValidationResult(false, ValidationMessages::DNS_RECORD_NOT_FOUND);

    }
}