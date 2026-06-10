<?php

declare(strict_types=1);

namespace App\Core\Http;

interface Response
{
    public function send(): void;
}
