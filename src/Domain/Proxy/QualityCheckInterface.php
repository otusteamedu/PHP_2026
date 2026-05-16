<?php

namespace App\Domain\Proxy;

interface QualityCheckInterface
{
    public function check(string $finalProduct): bool;
}
