<?php

declare(strict_types=1);

namespace App\Application\Proxy\QualityChecker;

use App\Domain\Proxy\QualityCheckInterface;

final readonly class MaxCheeseChecker implements QualityCheckInterface
{
    public function check(string $finalProduct): bool
    {
        $count = preg_match_all('/\bсыр\b/iu', $finalProduct);
        return $count < 4;
    }
}
