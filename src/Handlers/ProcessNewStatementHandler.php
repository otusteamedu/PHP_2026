<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Enum\StatementStatus;
use App\Mail\StatementNotificationService;
use App\Repository\StatementRequestRepository;
use App\Worker\Publisher;

final readonly class ProcessNewStatementHandler implements HandlerInterface
{
    public function __construct(
        private StatementRequestRepository $repository,
        private Publisher $processingPublisher,
        private StatementNotificationService $notifications,
    ) {}

    public function handle(array $data): void
    {
        $id = trim((string)($data['id'] ?? ''));
        if ($id === '') {
            throw new \InvalidArgumentException('Message must contain id');
        }

        $updated = $this->repository->updateStatus(
            $id,
            StatementStatus::New,
            StatementStatus::Processing,
        );

        if (!$updated) {
            return;
        }

        $statement = $this->repository->findById($id);
        if ($statement === null) {
            return;
        }

        $this->notifications->sendProcessing(
            $statement['email'],
            $statement['date_from'],
            $statement['date_to'],
            $statement['id'],
        );

        $this->processingPublisher->publish(json_encode(['id' => $id], JSON_UNESCAPED_UNICODE));
    }
}
