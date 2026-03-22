<?php

declare(strict_types=1);

namespace evgeny87\LeetCode\Service;

use evgeny87\LeetCode\DTO\ListNode;

/**
 * Сервис слияния двух отсортированных списков.
 * Реализовано согласно алгоритмическим принципам:
 * 1. Указатели для навигации.
 * 2. Чистые условия без оператора NOT (!).
 * 3. Цикл while для работы со связанными структурами.
 */
final readonly class MergeService
{
    /**
     * Вариант 1: In-Place (Алгоритмический)
     * Модифицирует существующие узлы, перенаправляя ссылки next.
     * Экономит память, но изменяет входные данные.
     */
    public function mergeInPlace(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        $dummy = new ListNode();
        $current = $dummy;

        // Кит 3: Цикл while для нелинейного движения
        while ($list1 !== null && $list2 !== null) {
            // Кит 2: Именованное условие без "!"
            $isFirstValueSmaller = $list1->val <= $list2->val;

            if ($isFirstValueSmaller === true) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }
            
            // Кит 1: Движение указателя
            $current = $current->next;
        }

        // Приклеиваем оставшуюся часть списка
        $current->next = $list1 ?? $list2;

        return $dummy->next;
    }

    /**
     * Вариант 2: Immutable (Архитектурный)
     * Создает абсолютно новые объекты ListNode.
     * Гарантирует, что исходные списки останутся нетронутыми.
     */
    public function mergeImmutable(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        $dummy = new ListNode();
        $current = $dummy;

        $p1 = $list1;
        $p2 = $list2;

        while ($p1 !== null && $p2 !== null) {
            $isFirstValueSmaller = $p1->val <= $p2->val;

            if ($isFirstValueSmaller === true) {
                $current->next = new ListNode($p1->val);
                $p1 = $p1->next;
            } else {
                $current->next = new ListNode($p2->val);
                $p2 = $p2->next;
            }
            $current = $current->next;
        }

        // Для полной иммутабельности копируем и остаток списка
        $remaining = $p1 ?? $p2;
        while ($remaining !== null) {
            $current->next = new ListNode($remaining->val);
            $remaining = $remaining->next;
            $current = $current->next;
        }

        return $dummy->next;
    }
}
