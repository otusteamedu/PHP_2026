<?php

namespace App\Domain\Entity;

interface Cookable
{
    public function prepare(): string;
}
