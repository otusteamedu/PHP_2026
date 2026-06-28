<?php

namespace App\MessageHandler;

use App\Enum\RequestStatus;
use App\Message\ProcessRequestMessage;
use App\Repository\ProcessingRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ProcessRequestHandler
{
    public function __construct(
        private readonly ProcessingRequestRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProcessRequestMessage $message): void
    {
        $request = $this->repository->find($message->requestId);
        if ($request === null) {
            $this->logger->warning('Request not found', ['id' => $message->requestId]);
            return;
        }

        $request->setStatus(RequestStatus::Processing);
        $this->em->flush();

        // Simulate background work
        sleep(2);
        $payload = $request->getPayload();
        $result = [
            'processed' => true,
            'sum' => array_sum(array_filter($payload, 'is_numeric')),
            'keys' => array_keys($payload),
        ];

        $request->setResult($result);
        $request->setStatus(RequestStatus::Done);
        $this->em->flush();

        $this->logger->info('Request processed', ['id' => $message->requestId]);
    }
}