<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Ports\JobRepositoryInterface;
use App\Domain\Entity\EmailValidationJob;

final readonly class GetEmailValidationStatusUseCase
{
    public function __construct(
        private JobRepositoryInterface $repository
    ) {}

    public function execute(int $id): ?EmailValidationJob
    {
        return $this->repository->find($id);
    }
}