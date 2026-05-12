1. Решение задачи Two Sum

```php
function twoSum(array $nums, int $target): array {
    $output = [];

    foreach ($nums as $key => $value) {
        $secondNum = $target - $value;
        if (isset($output[$secondNum])) {
            return [$output[$secondNum], $key];
        }

        $output[$value] = $key;

    }

    return [];
}
```

**Обоснование сложности**

1. Проход по массиву foreach ($nums as $key => $value) - если массив содержит n элементов, цикл выполнится максимум n раз. Сложность O(n)
2. Вычисление второго числа - обычная арифметическая операция, т.е. сложность O(1)
2. Проверка hash map isset($output[$secondNum]) - вычисляем хэш ключа и сразу получаем нужное значение, не перебирая весь массив, т.е. O(1)
3. Операция вставки в hash map $output[$value] = $key средняя сложность O(1)

**Итого:** общая сложность O(n) ∗ O(1) = O(n)


2. Решение задачи Sort Array by Increasing Frequency

```php
function frequencySort(array $nums): array {
    $hash = [];

    foreach ($nums as $num) {
        $hash[$num] = ($hash[$num] ?? 0) + 1;
    }

    usort($nums, function ($a, $b) use ($hash) {
        if ($hash[$a] === $hash[$b]) {
            if ($a > $b) {
                return -1;
            }

            if ($a < $b) {
                return 1;
            }

            return 0;
        }

        if ($hash[$a] < $hash[$b]) {
            return -1;
        }

        if ($hash[$a] > $hash[$b]) {
            return 1;
        }

        return 0;
    });

    return $nums;
}
```

**Обоснование сложности**

1. Подсчет частоты элемента в массиве - проходимся одним циклом, что занимает O(n) 
2. Сортировка массива nums - функция usort использует алгоритм со сложностью O(n log n). Внутри сравнения делается обращение к хэш таблице, доступ к которой занимает в среднем O(1)

**Итого:** общая сложность O(n) + O(n log n) = O(n log n).


3. Решение задачи Intersection of two arrays

```php
function intersection(array $nums1, array $nums2): array {
    $hash = [];
    foreach ($nums1 as $num1) {
        $hash[$num1] = true;
    }

    $result = [];
    foreach ($nums2 as $num2) {
        if (isset($hash[$num2])) {
            $result[] = $num2;
        }
    }
    return array_unique($result);
}
```

**Обоснование сложности**

1. Записываем наличие элементов в массиве (реализация hash set) - проходимся одним циклом, что занимает O(n) 
2. Ищем пересечение элементов в массиве nums2 - второй цикл O(m)
3. Получаем уникальные элементы с помощью функции array_unique - сложность O(k)

**Итого:** общая сложность O(n+m+k)

4. Решение задачи Largest Positive Integer That Exists With Its Negative

```php
function findMaxK(array $nums): int {
    $hash = [];
    $temp = -1;

    foreach ($nums as $num) {
        $hash[$num] = true;
    }

   
    foreach ($nums as $num) {
        if($num > 0 && isset($hash[-$num])) {
            if($num > $temp) {
                $temp = $num;
            }
        }
        
    }
    return $temp;
}
```

**Обоснование сложности**

1. Проход по массиву foreach ($nums as $num) - если массив содержит n элементов, цикл выполнится максимум n раз. Сложность O(n)
2. Операция вставки в hash map - $hash[$num] = true. Вычисляем hash ключа и сохраняем значение - сложность O(1)
Общая сложность первого цикла: O(n) ∗ O(1) = O(n)

3. Второй проход по массиву foreach ($nums as $num) (аналогично п.1) - сложность O(n)
4. Проверка числа $num > 0, проверка существования противоположного числла isset($hash[-$num]), проверка максимума if ($num > $max) - все по O(1)
Общая сложность второго цикла: O(n) ∗ O(1) = O(n)

**Итого:** общая сложность O(n)
