<?php

declare(strict_types=1);

namespace App\Domain\Enum;

use App\Domain\Decorator\BreadDecorator;
use App\Domain\Decorator\CheeseDecorator;
use App\Domain\Decorator\Decorator;
use App\Domain\Decorator\KetchupDecorator;
use App\Domain\Decorator\MeetDecorator;
use App\Domain\Decorator\OnionDecorator;
use App\Domain\Decorator\VegetablesDecorator;
use App\Domain\Entity\Cookable;

enum Ingredient
{
    case BREAD;
    case CHEESE;
    case KETCHUP;
    case MEAT;
    case VEGETABLES;
    case ONION;

    public function getDecorator(Cookable $product): Decorator
    {
        return match ($this) {
            self::ONION => new OnionDecorator($product),
            self::BREAD => new BreadDecorator($product),
            self::CHEESE => new CheeseDecorator($product),
            self::KETCHUP => new KetchupDecorator($product),
            self::MEAT => new MeetDecorator($product),
            self::VEGETABLES => new VegetablesDecorator($product),
        };
    }
}
