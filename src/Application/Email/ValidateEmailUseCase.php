<?php

declare(strict_types=1);

namespace Aharutyunyan\Hw\Application\Email;

use Aharutyunyan\Hw\Domain\Email\ValidationResult;
use Aharutyunyan\Hw\Domain\Email\ValueObject\Email;
use Aharutyunyan\Hw\Infrastructure\Validator\CompositeValidator;

final class ValidateEmailUseCase
{
    public function __construct(private CompositeValidator $validator)
    {
    }

    public function execute(string $email): ValidationResult
    {
        return $this->validator->validate(new Email($email));
    }
}