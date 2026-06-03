<?php

class Solution
{

    /**
     * @param int $numerator
     * @param int $denominator
     * @return String
     */
    function fractionToDecimal(int $numerator, int $denominator)
    {
        $result = '';

        // Проверяем будет ли результат отрицательным и если да, то ставим в начале минус и значения берем по модулю
        if (($numerator > 0 && $denominator < 0) || ($numerator < 0 && $denominator > 0)) {
            $result = '-';
            $numerator = abs($numerator);
            $denominator = abs($denominator);
        }

        // Получаем целую часть
        $result .= intdiv($numerator, $denominator);

        // Если остатка от отделения нет, то возвращаем результат
        if (($numerator % $denominator) === 0) {
            return $result;
        }

        $result .= '.';
        $hash = [];
        $numerator = $numerator % $denominator;
        while (true) {
            $numerator = (int)($numerator * 10);

            // Если делитель начинает повторяться, записываем дробь с периодом и выходим из цикла
            if (isset($hash[$numerator])) {
                $result .= '(' . implode('', $hash) . ')';
                break;
            }
            $hash[$numerator] = intdiv($numerator, $denominator);
            $numerator = $numerator % $denominator;

            // Если деление закончилось без остаткка, то записываем результат в дробную часть и выходим из цикла
            if ($numerator === 0) {
                $result .= implode('', $hash);
                break;
            }
        }

        return $result;
    }
}

$solution = new Solution;
// Example 1:
// Input: numerator = 1, denominator = 2
$result = $solution->fractionToDecimal(1, 2);
echo $result . PHP_EOL;
// Output: "0.5"


// Example 2:
// Input: numerator = 2, denominator = 1
$result = $solution->fractionToDecimal(2, 1);
echo $result . PHP_EOL;
// Output: "2"


// Example 3:
// Input: numerator = 4, denominator = 333
$result = $solution->fractionToDecimal(4, 333);
echo $result . PHP_EOL;
// Output: "0.(012)"

// Example 4:
// Input: numerator = 22, denominator = 7
$result = $solution->fractionToDecimal(22, 7);
echo $result . PHP_EOL;
// Output: "3.(142857)"

// Example 5:
// Input: numerator = -1, denominator = -2
$result = $solution->fractionToDecimal(-1, -2);
echo $result . PHP_EOL;
// Output: "0.5"

// Example 6:
// Input: numerator = 1, denominator = 97
$result = $solution->fractionToDecimal(1, 97);
echo $result . PHP_EOL;
// Output: "0.5"