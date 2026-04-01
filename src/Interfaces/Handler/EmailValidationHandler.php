<?php

declare(strict_types=1);

namespace Aharutyunyan\Hw\Interfaces\Handler;

use Aharutyunyan\Hw\Application\Email\ValidateEmailUseCase;
use Aharutyunyan\Hw\Infrastructure\Validator\CompositeValidator;
use Aharutyunyan\Hw\Infrastructure\Validator\Strategy\DisposableEmailValidator;
use Aharutyunyan\Hw\Infrastructure\Validator\Strategy\DomainValidator;
use Aharutyunyan\Hw\Infrastructure\Validator\Strategy\SyntaxValidator;

class EmailValidationHandler
{
    public function handle(string $emailToTest = null): array
    {
        $email = $emailToTest ?? $this->generateEmail();

        $validator = new CompositeValidator();
        $validator->add(new SyntaxValidator());
        $validator->add(new DomainValidator());
        $validator->add(new DisposableEmailValidator());

        $useCase = new ValidateEmailUseCase($validator);

        return (array) $useCase->execute($email);
    }

    private function generateEmail(): string
    {
        $domains = ['example.com', 'mailinator.com', 'test.com'];
        $name = 'user' . rand(1000, 9999);
        $domain = $domains[array_rand($domains)];
        return "$name@$domain";
    }
}