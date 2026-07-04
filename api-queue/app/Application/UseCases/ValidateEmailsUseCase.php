<?php

declare (strict_types = 1);

namespace App\Application\UseCases;

use App\Domain\EmailValidator;

class ValidateEmailsUseCase
{
    public function __construct(private readonly EmailValidator $validator)
    {}

    public function execute(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            $validation = $this->validator->validate($email);
            $results[]  = [
                'email'   => $email,
                'status'  => $validation->isValid ? 'valid' : 'invalid',
                'message' => $validation->message,
            ];
        }
        return $results;
    }
}
