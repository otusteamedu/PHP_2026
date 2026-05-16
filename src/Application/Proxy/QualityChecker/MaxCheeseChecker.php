<?php

declare(strict_types=1);

namespace App\Application\Proxy\QualityChecker;

use App\Domain\Proxy\QualityCheckInterface;

class MaxCheeseChecker implements QualityCheckInterface
{
    public function check(string $finalProduct): bool
    {
        $count = preg_match_all('/\bcheese\b/i', $finalProduct);
        return $count < 4;
    }
}
