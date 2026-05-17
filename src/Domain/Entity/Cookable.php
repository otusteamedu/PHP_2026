<?php

namespace App\Domain\Entity;

interface Cookable
{
    public function getName(): string;
    public function prepare(): string;
}
