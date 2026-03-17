<?php

declare(strict_types=1);

namespace App\app\Services\Validation;

use App\app\Services\Validation\Contracts\ValidationInterface;
use Exception;

class ParenthesesValidator implements ValidationInterface
{
    public function __construct(
        public ?string $pattern = null,
    )
    {
    }

    /**
     * @throws Exception
     */
    public function handle(string $str): array
    {
        if (empty($str)) {
            throw new Exception("String validation requires a string");
        }

        $stack = [];

        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] === '(') {
                $stack[] = $str[$i];
            } elseif ($str[$i] === ')') {
                if (empty($stack)) {
                    return [
                        'valid' => false,
                        'status' => 400,
                        'error' => 'Unmatched closing parenthesis at ' . $i
                    ];
                }

                if ($stack[count($stack) - 1] === '(') {
                    array_pop($stack);
                } else {
                    $stack[] = $str[$i];
                }
            }
        }

        if (count($stack) > 0) {
            return [
                'valid' => false,
                'status' => 400,
                'error' => 'Unmatched parentheses: ' . count($stack) . ' remaining'
            ];
        }

        return [
            'valid' => true,
            'status' => 200,
            'message' => 'String check was passed'
        ];
    }
}
