<?php

declare(strict_types=1);

namespace App\Statement;

use JsonException;

/**
 * A user's request to generate a bank statement for a date range.
 * This is the message body that travels through the queue.
 */
final readonly class StatementRequest
{
    public function __construct(
        public string $id,
        public string $account,
        public string $dateFrom,
        public string $dateTo,
        public string $email,
        public string $requestedAt,
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'account'      => $this->account,
            'date_from'    => $this->dateFrom,
            'date_to'      => $this->dateTo,
            'email'        => $this->email,
            'requested_at' => $this->requestedAt,
        ];
    }

    /**
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @throws JsonException
     */
    public static function fromJson(string $json): self
    {
        /** @var array<string, string> $data */
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return new self(
            id:          (string) ($data['id'] ?? ''),
            account:     (string) ($data['account'] ?? ''),
            dateFrom:    (string) ($data['date_from'] ?? ''),
            dateTo:      (string) ($data['date_to'] ?? ''),
            email:       (string) ($data['email'] ?? ''),
            requestedAt: (string) ($data['requested_at'] ?? ''),
        );
    }
}