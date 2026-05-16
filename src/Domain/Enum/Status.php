<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum Status
{
    case READY_TO_COOK;
    case COOKING;
    case DONE;
    case FAILED;
}
