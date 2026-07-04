<?php

declare (strict_types = 1);

namespace App\Application\UseCases;

use App\Application\DTO\ValidationJobPayload;
use App\Application\Exceptions\InvalidValidationRequestException;
use App\Application\Interfaces\QueuePublisherInterface;
use App\Application\Ports\JobRepositoryInterface;
use App\Domain\EmailValidator;
use App\Domain\Entity\EmailValidationJob;
use App\Domain\Enum\JobStatus;
use App\Domain\ValidationRequest;
use App\Shared\RequestMessages;

final readonly class EnqueueEmailValidationUseCase
{
    public function __construct(private readonly EmailValidator $validator, private readonly QueuePublisherInterface $queuePublisher, private readonly int $maxEmails, private readonly JobRepositoryInterface $repository)
    {}

    public function execute(ValidationRequest $request): int
    {
        if (empty($request->emails)) {
            throw new InvalidValidationRequestException(RequestMessages::MISSING_EMAIL_ADDRESS->value);
        }

        if (count($request->emails) > $this->maxEmails) {
            throw new InvalidValidationRequestException(RequestMessages::ERROR_EADDRES_COUNT->value);
        }

        $reportValidation = $this->validator->validate($request->reportEmail);

        if (! $reportValidation->isValid) {
            throw new InvalidValidationRequestException(RequestMessages::ERROR_REPORT_EMAIL->value);
        }

        $job = new EmailValidationJob(
            id: null,
            status: JobStatus::QUEUED,
            emails: $request->emails,
            reportEmail: $request->reportEmail,
            createdAt: new \DateTimeImmutable(),
            updatedAt: new \DateTimeImmutable()
        );

        $jobId = $this->repository->create($job);

        $this->queuePublisher->publish(new ValidationJobPayload($jobId, $request->emails, $request->reportEmail));

        return $jobId;
    }
}
