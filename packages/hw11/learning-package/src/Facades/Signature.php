<?php

namespace Hw11\LearningPackage\Facades;

use Hw11\LearningPackage\Contracts\SignatureBuilderInterface;
use Illuminate\Support\Facades\Facade;

class Signature extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SignatureBuilderInterface::class;
    }
}
