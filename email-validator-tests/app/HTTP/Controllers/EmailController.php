<?php

declare(strict_types=1);

namespace App\HTTP\Controllers;

use App\Application\ValidateEmailsUseCases;
use App\Shared\RequestParser;
use App\Shared\ResponseParser;
use App\Infrastructure\Views\Views;

class EmailController
{  
    public function __construct(
        private readonly ValidateEmailsUseCases $useCase,
        private readonly RequestParser $requestParser,
        private readonly ResponseParser $responseParser)
    {}

    public function index(): void
    {
        Views::view('emails-validator/index');
    }

    public function check(): void
    {
        try {           
            $emails = $this->requestParser->parse(file_get_contents('php://input'));            
         
            $results = $this->useCase->execute($emails);
           
            $this->responseParser->parse(200, [
                'success' => true,
                'results' => $results
            ]);
            
        } catch (\Throwable $e) {
            $this->responseParser->parse($e->getCode() ?: 400, [
                'success' => false,
                'error'   => $e->getMessage()
            ]);
        }
    }
}
