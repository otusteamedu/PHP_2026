<?php

declare(strict_types=1);

namespace App\app\Services\Validation;

use Exception;

class ParenthesesValidator extends StringValidationService
{
    public function __construct(
        public readonly ?string $pattern = null,
    )
    {
        parent::__construct($this->pattern);
    }

    /**
     * @throws Exception
     */
    public function handle(string $str): array
    {
        if (empty($this->str)) {
            throw new Exception("String validation requires a string");
        }

        $stack = [];

        for ($i = 0; $i < strlen($this->str); $i++) {
            if ($this->str[$i] === '(') {
                $stack[] = $this->str[$i];
            } elseif ($this->str[$i] === ')') {
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
                    $stack[] = $this->str[$i];
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
