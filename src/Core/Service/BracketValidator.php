<?php

namespace App\Core\Service;

use App\Core\Exception\ValidationException;
use InvalidArgumentException;

class BracketValidator
{
    public function validate(string $string): bool
    {
        if ($string === '') {
            throw new InvalidArgumentException('String is empty');
        }

        $balance = 0;
        $strLen = strlen($string);

        for ($i = 0; $i < $strLen; $i++) {
            $char = $string[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
            } else {
                throw new InvalidArgumentException('Invalid character detected');
            }

            if ($balance < 0) {
                throw new ValidationException('Brackets are invalid');
            }
        }

        return $balance === 0;
    }
}
