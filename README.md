# AErmolenko/hw18

**LeetCode практикум №2.**

1. https://leetcode.com/problems/intersection-of-two-linked-lists/
2. https://leetcode.com/problems/fraction-to-recurring-decimal/

## Решения

### 1. Intersection of Two Linked Lists — `IntersectionOfTwoLinkedLists.php`

Найти узел, в котором сходятся два односвязных списка (сравнение по ссылке).

**Сложность:**
Время - O(a + b): каждый указатель проходит не более a + b узлов.

### 2. Fraction to Recurring Decimal — `FractionToRecurringDecimal.php`

Перевести дробь `numerator / denominator` в десятичную строку, периодическую
часть обернуть в `()`.

**Сложность:** 
Время - O(d): цикл гарантированно завершается за O(d) итераций.

## Запуск

```bash
php src/IntersectionOfTwoLinkedLists.php
php src/FractionToRecurringDecimal.php
```
