<?php

namespace Aharutyunyan\Hw\Infrastructure\Validator;

use Aharutyunyan\Hw\Domain\Email\ValidationResult;
use Aharutyunyan\Hw\Domain\Email\Validator\EmailValidatorInterface;
use Aharutyunyan\Hw\Domain\Email\ValueObject\Email;

final class CompositeValidator implements EmailValidatorInterface
{
    private array $validators = [];

    public function add(EmailValidatorInterface $validator): self
    {
        $this->validators[] = $validator;
        return $this;
    }

    public function validate(Email $email): ValidationResult
    {
        $details = [];

        foreach ($this->validators as $validator) {
            $result = $validator->validate($email);

            $details[get_class($validator)] = [
                'valid' => $result->isValid,
                'reason' => $result->reason
            ];

            if (!$result->isValid) {

                return new ValidationResult(
                    false,
                    $result->reason,
                    $details
                );
            }
        }

        return new ValidationResult(true, details: $details);
    }
}