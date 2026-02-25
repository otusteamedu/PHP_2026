<?php

namespace Aharutyunyan\Hw\Infrastructure\Validator\Strategy;

use Aharutyunyan\Hw\Domain\Email\ValidationResult;
use Aharutyunyan\Hw\Domain\Email\Validator\EmailValidatorInterface;
use Aharutyunyan\Hw\Domain\Email\ValueObject\Email;

final class DomainValidator implements EmailValidatorInterface
{
    public function validate(Email $email): ValidationResult
    {
        $domain = explode('@', $email->getEmail())[1] ?? '';

        if (checkdnsrr($domain)) {
            return new ValidationResult(true);
        }

        return new ValidationResult(false, 'Domain has no mail servers');
    }}