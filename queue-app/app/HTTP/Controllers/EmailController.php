<?php

declare(strict_types=1);

namespace App\HTTP\Controllers;

use App\Application\UseCases\EnqueueEmailValidationUseCase;
use App\Shared\RequestParser;
use App\Shared\ResponseParser;
use App\Infrastructure\Views\Views;
use App\Application\Exceptions\InvalidValidationRequestException;

class EmailController
{  
    public function __construct(
        private readonly EnqueueEmailValidationUseCase $useCase,
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
         
            $this->useCase->execute($emails);
           
            $this->responseParser->parse(202, [
                'success' => true,
                'message' => 'Отчет о результатах проверки электронных адресов будет отправлен на указанную почту.'                            
            ]);
            
        } catch (InvalidValidationRequestException $e) {
            $this->responseParser->parse(400, [
                'success' => false,
                'error'   => $e->getMessage()
            ]);
        } catch (\Throwable $e) {
            // Инфраструктурные ошибки (RabbitMQ упал, сеть моргнула), подумать над логированием           
            $this->responseParser->parse(500, [
                'success' => false,
                'error'   => $e->getMessage()
            ]);
        }
    }
}
