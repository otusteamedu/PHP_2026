<?php

declare(strict_types=1);

namespace App\Application\Proxy;

use App\Domain\Entity\Cookable;
use App\Domain\Proxy\CookInterface;

final readonly class Cook implements CookInterface
{
    public function cook(Cookable $product): string
    {
        return $product->prepare();
    }
}
