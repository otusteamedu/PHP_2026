<?php

declare(strict_types=1);

namespace App\Repository;

use App\Enum\StatementStatus;
use PDO;

final readonly class StatementRequestRepository
{
    public function __construct(
        private PDO $pdo,
    ) {}

    public function create(string $email, string $dateFrom, string $dateTo): string
    {
        $id = $this->generateUuid();
        $now = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            'INSERT INTO statement_requests (id, email, date_from, date_to, status, created_at, updated_at)
             VALUES (:id, :email, :date_from, :date_to, :status, :created_at, :updated_at)',
        );
        $stmt->execute([
            'id' => $id,
            'email' => $email,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'status' => StatementStatus::New->value,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $id;
    }

    /**
     * @return array{
     *     id: string,
     *     email: string,
     *     date_from: string,
     *     date_to: string,
     *     status: string,
     *     created_at: string,
     *     updated_at: string
     * }|null
     */
    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, email, date_from, date_to, status, created_at, updated_at
             FROM statement_requests WHERE id = :id',
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function updateStatus(string $id, StatementStatus $from, StatementStatus $to): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE statement_requests SET status = :to, updated_at = NOW() WHERE id = :id AND status = :from RETURNING id',
        );
        $stmt->execute([
            'id' => $id,
            'from' => $from->value,
            'to' => $to->value,
        ]);

        return $stmt->fetch() !== false;
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
