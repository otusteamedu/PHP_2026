<?php

declare (strict_types = 1);

namespace App\Infrastructure\Container;

use App\Application\ValidateEmailsUseCases;
use App\Domain\EmailValidator;
use App\HTTP\Controllers\EmailController;
use App\Infrastructure\Validation\EmailValidation;
use App\Infrastructure\Validation\MxValidation;
use App\Shared\RequestParser;
use App\Shared\ResponseParser;

class Container
{
    public function emailController(): EmailController
    {
        return new EmailController(
            $this->validateEmailsUseCase(),
            $this->requestParser(),
            $this->responseParser()
        );
    }

    private function validateEmailsUseCase(): ValidateEmailsUseCases
    {
        return new ValidateEmailsUseCases($this->emailValidator());
    }

    private function emailValidator(): EmailValidator
    {
        return new EmailValidator([new EmailValidation(), new MxValidation()]);
    }

    private function requestParser(): RequestParser
    {
        $config = require BASE_PATH . '/config/app.php';

        return new RequestParser($config['max_emails_in_request']);
    }

    private function responseParser(): ResponseParser
    {
        return new ResponseParser();
    }
}
