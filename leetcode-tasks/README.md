# Решение задачи Fraction to Recurring Decimal

```
function getFractionType(int $numerator, int $denominator): string
{
    if ($numerator == 0) {
        return "0";
    }

    $result = '';

    if (($numerator < 0) !== ($denominator < 0)) {
        $result .= '-';
    }

    $numerator = abs($numerator);

    $denominator = abs($denominator);

    $result .= intdiv($numerator, $denominator);

    $temp = $numerator % $denominator;

    if ($temp === 0) {
        return $result;
    }

    $result .= '.';
    $arr = [];

    while ($temp !== 0) {
        if (isset($arr[$temp])) {
            $position = $arr[$temp];
            $result = substr($result, 0, $position) . '(' . substr($result, $position) . ')';
            break;
        }

        $arr[$temp] = strlen($result);

        $temp *= 10;

        $digit = intdiv($temp, $denominator);

        $result .= $digit;

        $temp %= $denominator;
    }

    return $result;
}

```

**Обоснование сложности**

- if ($numerator == 0) - обычное сравнение двух чисел, которое выполняется один раз. Сложность O(1)

- if (($numerator < 0) !== ($denominator < 0)) - два сравнения и логическая операция. Сложность O(1)

- $result .= intdiv($numerator, $denominator); и $temp = $numerator % $denominator; - обычные арифметические операции. Сложность O(1)

- while ($temp !== 0) - цикл деления выполняется до тех пор пока остаток не станет 0 (т.е. конечная дробь) или остаток не повторится (периодическая дробь).

- на каждой итерации осуществляются операции типа isset($arr[$temp]), $arr[$temp] = strlen($result); или $remainder *= 10; intdiv($remainder, $denominator); $remainder %= $denominator; (умножение, деление и получение остатка). Сложность O(1) у всех операций.

- вставка скобок при обнаружении периода $result = substr($result, 0, $position)... если длина строки результата равна n, операция требует прохода по строке, значит сложжность O(n)

В итоге: временная сложность составляет O(n) + O(n) = O(n). Почему? Основной цикл выполняется n раз, где n — количество цифр после запятой до окончания дроби или обнаружения периода. Каждая итерация содержит только операции O(1). В конце может быть выполнена операция вставки скобок.

# Решение задачи Intersection of Two Linked Lists

```
class Solution {
    function getIntersectionNode($headA, $headB) {        
        return $this->helper($headA, $headB, $headA, $headB);
    }

    private function helper($A, $B, $headA, $headB) {        
        if ($A === $B) {
            return $A;
        }
       
        $nextA = ($A === null) ? $headB : $A->next;
        $nextB = ($B === null) ? $headA : $B->next;
        
        return $this->helper($nextA, $nextB, $headA, $headB);
    }
}
```

**Обоснование сложности**
- временная сложность O(m + n) - m - длина списка А, n - длина списка B 
- пространственная сложность O(m + n)

**Если делать через цикл while**

- временная сложность O(m + n)
- пространственная сложность O(1), т.е. существует всего две переменные-указателя, не создаются новые структуры данных, поэтому не тратится дополнительная память, как с рекурсией.