<?php

namespace App\Application\UseCases;

use App\Application\DTO\ValidationJobPayload;
use App\Application\DTO\ValidationReportResult;
use App\Application\Ports\EmailSenderInterface;
use App\Application\Ports\PdfGeneratorInterface;

final readonly class ProcessValidationReportUseCase
{
    public function __construct(
        private ValidateEmailsUseCase $validateEmailsUseCase,
        private PdfGeneratorInterface $pdfGenerator,
        private EmailSenderInterface $emailSender,
    ) {}

    public function execute(ValidationJobPayload $payload): ValidationReportResult
    {
        $validationResults = $this->validateEmailsUseCase->execute($payload->emails);

        $pdfContent = $this->pdfGenerator->generate(validationResults: $validationResults);

        $subject = 'Отчет проверки email';
        $body = 'Во вложении находится отчет о результатах проверки электронных адресов.';

        $date = (new \DateTimeImmutable())->format('Ymd_His');

        $fileName = sprintf('report_%s.pdf', $date);

        $this->emailSender->send(
            to: $payload->reportEmail,
            subject: $subject,
            body: $body,
            pdfContent: $pdfContent,
            fileName: $fileName
        );

        $errorsCount = count(array_filter($validationResults, fn($r) => $r['status'] === 'invalid'));

        return new ValidationReportResult(total:count($validationResults), errors:$errorsCount, pdfSize:strlen($pdfContent));
    }
}