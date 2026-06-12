<?php

declare(strict_types=1);

/**
 * Given two integers representing the numerator and denominator of a fraction, return the fraction in string format.
 *
 * If the fractional part is repeating, enclose the repeating part in parentheses
 *
 * If multiple answers are possible, return any of them.
 *
 * It is guaranteed that the length of the answer string is less than 104 for all the given inputs.
 *
 * Note that if the fraction can be represented as a finite length string, you must return it.
 *
 *
 *
 * Example 1:
 * Input: numerator = 1, denominator = 2
 * Output: "0.5"
 *
 * Example 2:
 * Input: numerator = 2, denominator = 1
 * Output: "2"
 *
 * Example 3:
 * Input: numerator = 4, denominator = 333
 * Output: "0.(012)"
 *
 *
 * Constraints:
 * -231 <= numerator, denominator <= 231 - 1
 * denominator != 0
 */
class Solution {

    private bool $first = true;
    private array $hash = [];

    /**
     * Сложность O(2n) => O(n), где n - количество цифр в итоговом остатке.
     *
     * @param Integer $numerator
     * @param Integer $denominator
     * @return String
     */
    function fractionToDecimal($numerator, $denominator) {
        if ($numerator === 0) {
            return "0";
        }

        $sign = '';
        if ($numerator < 0 && $denominator > 0 || $numerator > 0 && $denominator < 0) {
            $sign = '-';
        }

        $numerator = abs($numerator);
        $denominator = abs($denominator);

        $quotient = $sign . (int) floor($numerator / $denominator);
        $remainder = $numerator % $denominator;

        if ($remainder === 0) {
            $this->hash[$remainder] = $quotient;

            if (count($this->hash) === 1) {
                return implode('', $this->hash);
            }
            $first = array_shift($this->hash);
            return $first . '.' . implode('', $this->hash);

        }

        if (array_key_exists($numerator, $this->hash)) {
            $this->hash[$numerator] = "({$quotient}";
            $this->hash[] = ")";
            $first = array_shift($this->hash);
            return $first . '.' . implode('', $this->hash);
        }

        if ($this->first) {
            $this->hash['first'] = $quotient;
            $this->first = false;
        } else {
            $this->hash[$numerator] = $quotient;
        }


        return $this->fractionToDecimal($remainder * 10, $denominator);
    }
}
