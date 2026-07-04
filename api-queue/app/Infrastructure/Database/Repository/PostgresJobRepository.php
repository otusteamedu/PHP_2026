<?php
declare (strict_types = 1);

namespace App\Infrastructure\Database\Repository;

use App\Application\Ports\JobRepositoryInterface;
use App\Domain\Entity\EmailValidationJob;
use App\Domain\Enum\JobStatus;
use DateTimeImmutable;
use PDO;

final readonly class PostgresJobRepository implements JobRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {}

    public function create(EmailValidationJob $job): int
    {
        $sql = "
        INSERT INTO email_validation_jobs
        (status, emails, report_email, created_at, updated_at)
        VALUES
        (:status, :emails, :report_email, :created_at, :updated_at)
        RETURNING id
    ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'status'       => $job->status->value,
            'emails'       => json_encode($job->emails),
            'report_email' => $job->reportEmail,
            'created_at'   => $job->createdAt->format('Y-m-d H:i:s'),
            'updated_at'   => $job->updatedAt->format('Y-m-d H:i:s'),
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function find(int $id): ?EmailValidationJob
    {
        $sql = "SELECT id, status, emails, report_email, created_at, updated_at
                FROM email_validation_jobs WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (! $row) {
            return null;
        }

        return new EmailValidationJob(
            id: $row['id'],
            status: JobStatus::from($row['status']),
            emails: json_decode($row['emails'], true, 512, JSON_THROW_ON_ERROR),
            reportEmail: $row['report_email'],
            createdAt: new DateTimeImmutable($row['created_at']),
            updatedAt: new DateTimeImmutable($row['updated_at'])
        );
    }

    public function updateStatus(int $id, JobStatus $status): void
    {
        $sql = "UPDATE email_validation_jobs
                SET status = :status, updated_at = :updated_at
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id'         => $id,
            'status'     => $status->value,
            'updated_at' => (new DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
