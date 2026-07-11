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
        $domainPart = strrchr($email, "@");

        if (!$domainPart) {
            return new ValidationResult(false, ValidationMessages::UNKNOWN_ERROR->value);
        }

        $domain = substr($domainPart, 1);

        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return new ValidationResult(false, ValidationMessages::INVALID_DOMAIN->value);
        }

        $hasMx = checkdnsrr($domain) || !empty(@dns_get_record($domain, DNS_MX));

        return $hasMx
            ? new ValidationResult(true)
            : new ValidationResult(false, ValidationMessages::DNS_RECORD_NOT_FOUND->value);

    }
}