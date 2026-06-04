<?php

declare(strict_types=1);

/**
 * LeetCode 166. Fraction to Recurring Decimal
 * https://leetcode.com/problems/fraction-to-recurring-decimal/
 *
 * Задача:
 *      Дано numerator и denominator (целые). Вернуть строку с десятичным
 *      представлением дроби. Если дробная часть периодическая, тогда обернуть период
 *      в круглые скобки.
 *
 *      Примеры:
 *      1 / 2 = "0.5"
 *      2 / 1 = "2"
 *      4 / 333 = "0.(012)"
 *      1 / 6 = "0.1(6)"
 *
 * Сложность:
 *      Время - O(d): цикл гарантированно завершается за O(d) итераций.
 */

class Solution
{
    /**
     * @param int $numerator
     * @param int $denominator
     * @return string
     */
    public function fractionToDecimal(int $numerator, int $denominator): string
    {
        if ($numerator === 0) {
            return '0';
        }

        $result = '';

        if (($numerator < 0) !== ($denominator < 0)) {
            $result .= '-';
        }

        $num = abs($numerator);
        $den = abs($denominator);

        $result .= intdiv($num, $den);
        $remainder = $num % $den;

        if ($remainder === 0) {
            return $result;
        }

        $result .= '.';
        $seen = [];

        while ($remainder !== 0) {
            if (isset($seen[$remainder])) {
                $pos = $seen[$remainder];

                return substr($result, 0, $pos) . '(' . substr($result, $pos) . ')';
            }

            $seen[$remainder] = strlen($result);
            $remainder *= 10;
            $result .= intdiv($remainder, $den);
            $remainder %= $den;
        }

        return $result;
    }
}

$solution = new Solution();

echo $solution->fractionToDecimal(1, 2), PHP_EOL;    // 0.5
echo $solution->fractionToDecimal(2, 1), PHP_EOL;    // 2
echo $solution->fractionToDecimal(4, 333), PHP_EOL;  // 0.(012)
echo $solution->fractionToDecimal(1, 6), PHP_EOL;    // 0.1(6)
echo $solution->fractionToDecimal(0, 5), PHP_EOL;    // 0
echo $solution->fractionToDecimal(-1, 2), PHP_EOL;   // -0.5
echo $solution->fractionToDecimal(-50, 8), PHP_EOL;  // -6.25
echo $solution->fractionToDecimal(7, -12), PHP_EOL;  // -0.58(3)