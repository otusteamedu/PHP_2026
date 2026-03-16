<?php

declare(strict_types=1);

namespace App\app\Http\Controllers;

use App\app\Http\Response\HttpResponse;
use App\app\Services\Validation\Contracts\ValidationInterface;

class CheckRandomStringController
{
    protected array $randomStrings = [
        '((()))',
        '((()()()())))',
        ')()(())(',
        '()()()()',
    ];

    public function __construct(
        protected ValidationInterface $validator,
    )
    {
    }

    public function execute(?string $str = null): false|string
    {
        if ($str === null) {
            $key = array_rand($this->randomStrings);
            $str = $this->randomStrings[$key];
        }

        $result = $this->validator->handle($str);

        return HttpResponse::create($result);
    }
}
