<?php

namespace App;

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
                return false;
            }
        }

        return $balance === 0;
    }
}
