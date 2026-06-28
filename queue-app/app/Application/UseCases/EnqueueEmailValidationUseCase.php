<?php

declare (strict_types = 1);

namespace App\Application\UseCases;

use App\Domain\ValidationRequest;
use App\Shared\RequestMessages;
use App\Application\DTO\ValidationJobPayload;
use App\Application\Interfaces\QueuePublisherInterface;
use App\Domain\EmailValidator;
use App\Application\Exceptions\InvalidValidationRequestException;

final readonly class EnqueueEmailValidationUseCase
{
    public function __construct(private readonly EmailValidator $validator, private readonly QueuePublisherInterface $queuePublisher, private readonly int $maxEmails)
    {}

    public function execute(ValidationRequest $request): void
    {
        if (empty($request->emails)) {
            throw new InvalidValidationRequestException(RequestMessages::MISSING_EMAIL_ADDRESS->value);
        }

        if (count($request->emails) > $this->maxEmails) {
            throw new InvalidValidationRequestException(RequestMessages::ERROR_EADDRES_COUNT->value);
        }
      
        $reportValidation = $this->validator->validate($request->reportEmail);

        if (!$reportValidation->isValid) {
            throw new InvalidValidationRequestException(RequestMessages::ERROR_REPORT_EMAIL->value);
        }        

        $this->queuePublisher->publish(new ValidationJobPayload($request->emails, $request->reportEmail));        
    }
}
