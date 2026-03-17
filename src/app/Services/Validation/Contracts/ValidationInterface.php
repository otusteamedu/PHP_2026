<?php

namespace App\app\Services\Validation\Contracts;

interface ValidationInterface
{
    public function handle(string $str): array;
}
