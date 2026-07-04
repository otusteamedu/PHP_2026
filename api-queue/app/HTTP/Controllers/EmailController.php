<?php

declare (strict_types = 1);

namespace App\HTTP\Controllers;

use App\Application\Exceptions\InvalidValidationRequestException;
use App\Application\UseCases\EnqueueEmailValidationUseCase;
use App\Application\UseCases\GetEmailValidationStatusUseCase;
use App\Shared\RequestParser;
use App\Shared\ResponseParser;
use App\Domain\Enum\JobStatus;

class EmailController
{
    public function __construct(
        private readonly EnqueueEmailValidationUseCase $useCase,
        private readonly RequestParser $requestParser,
        private readonly ResponseParser $responseParser,
        private readonly GetEmailValidationStatusUseCase $statusUseCase) {}

    public function status(string $id): void
    {
        $job = $this->statusUseCase->execute((int)$id);

        header('Content-Type: application/json');

        if ($job === null) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Данные не найдены',
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'data'    => [
                'request_id' => $job->id,
                'status'     => $job->status->value,
                'created_at' => $job->createdAt->format(DATE_ATOM),
                'updated_at' => $job->updatedAt->format(DATE_ATOM),
            ],
        ]);
    }

    public function check(): void
    {
        try {
            $emails = $this->requestParser->parse(file_get_contents('php://input'));

            $jobId = $this->useCase->execute($emails);

            $this->responseParser->parse(202, [
                'success' => true,
                'request_id' => $jobId,
                'status' => JobStatus::QUEUED->value,
                'message' => 'Запрос поставлен в очередь.'
            ]);

        } catch (InvalidValidationRequestException $e) {
            $this->responseParser->parse(400, [
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {            
            $this->responseParser->parse(500, [
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
