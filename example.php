<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use evgeny87\LeetCode\DTO\ListNode;
use evgeny87\LeetCode\Service\MergeService;

/**
 * Точка входа для демонстрации решения Merge Two Sorted Lists.
 * Демонстрирует разницу между In-Place и Immutable подходами.
 */

$service = new MergeService();

// Вспомогательная функция для вывода списка (The evgeny87 Way - простая и понятная)
$printList = function (?ListNode $head): void {
    $current = $head;
    $result = [];
    while ($current !== null) {
        $result[] = $current->val;
        $current = $current->next;
    }
    echo implode(' -> ', $result) ?: 'Empty List';
    echo PHP_EOL;
};

// Подготовка данных: Список 1 (1 -> 2 -> 4) и Список 2 (1 -> 3 -> 4)
$list1 = new ListNode(1, new ListNode(2, new ListNode(4)));
$list2 = new ListNode(1, new ListNode(3, new ListNode(4)));

echo "Исходный список 1: "; $printList($list1);
echo "Исходный список 2: "; $printList($list2);
echo "----------------------------------------" . PHP_EOL;

// 1. Тестируем Immutable вариант (Архитектурный)
// Мы вызываем его первым, так как он не портит исходные списки
echo "Вариант Immutable (создание новых объектов):" . PHP_EOL;
$immutableResult = $service->mergeImmutable($list1, $list2);
echo "Результат: "; $printList($immutableResult);
echo "Проверка сохранности list1 после Immutable: "; $printList($list1);
echo "----------------------------------------" . PHP_EOL;

// 2. Тестируем In-Place вариант (Алгоритмический)
// ВНИМАНИЕ: После этого вызова $list1 и $list2 будут перестроены
echo "Вариант In-Place (перекидывание указателей):" . PHP_EOL;
$inPlaceResult = $service->mergeInPlace($list1, $list2);
echo "Результат: "; $printList($inPlaceResult);
echo "Проверка сохранности list1 после In-Place (будет изменен): "; $printList($list1);
