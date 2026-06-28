<?php

declare (strict_types = 1);

namespace App\Application\DTO;

final readonly class ValidationJobPayload
{
    public function __construct(
        public array $emails,
        public string $reportEmail,        
    ) {}

    public static function fromArray(array $data): self {
        return new self(
            $data['emails'] ?? [],
            $data['report_email'] ?? '',            
        );
    }

    public function toArray(): array {
        return [
            'emails' => $this->emails,
            'report_email' => $this->reportEmail            
        ];
    }
}
