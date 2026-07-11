<?php

declare (strict_types = 1);

namespace App\Application\DTO;

final readonly class ValidationJobPayload
{
    public function __construct(
        public int $jobId,
        public array $emails,
        public string $reportEmail,        
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['jobId'],
            $data['emails'] ?? [],
            $data['report_email'] ?? '',            
        );
    }

    public function toArray(): array {
        return [
            'jobId' => $this->jobId,
            'emails' => $this->emails,
            'report_email' => $this->reportEmail            
        ];
    }
}
