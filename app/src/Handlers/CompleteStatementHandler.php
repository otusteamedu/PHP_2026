<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Enum\StatementStatus;
use App\Mail\StatementNotificationService;
use App\Repository\StatementRequestRepository;

final readonly class CompleteStatementHandler implements HandlerInterface
{
    public function __construct(
        private StatementRequestRepository $repository,
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
            StatementStatus::Processing,
            StatementStatus::Completed,
        );

        if (!$updated) {
            return;
        }

        $statement = $this->repository->findById($id);
        if ($statement === null) {
            return;
        }

        $this->notifications->sendCompleted(
            $statement['email'],
            $statement['date_from'],
            $statement['date_to'],
            $statement['id'],
        );
    }
}
