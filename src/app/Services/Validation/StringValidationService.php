<?php

declare(strict_types=1);

namespace App\app\Services\Validation;

use App\app\Services\Validation\Contracts\ValidationInterface;
use Exception;

abstract class StringValidationService implements ValidationInterface
{
    /**
     * @throws Exception
     */
    public function __construct(
        public readonly ?string $pattern = null,
    )
    {
    }
}
