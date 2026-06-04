<?php

declare(strict_types=1);

/**
 *  LeetCode 160. Intersection of Two Linked Lists
 *  https://leetcode.com/problems/intersection-of-two-linked-lists/
 *  Задача:
 *      Даны головы двух односвязных списков. Найти узел, в котором они пересекаются
 *      (сравнение по ссылке, не по значению). Если пересечения нет - вернуть null.
 *  Сложность:
 *      Время - O(a + b): каждый указатель проходит не более a + b узлов.
 */

class ListNode
{
    public int $val;
    public ?ListNode $next;

    public function __construct(int $val = 0, ?self $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution
{
    /**
     * @param ListNode|null $headA
     * @param ListNode|null $headB
     * @return ListNode|null
     */
    public function getIntersectionNode(?ListNode $headA, ?ListNode $headB): ?ListNode
    {
        if ($headA === null || $headB === null) {
            return null;
        }

        $a = $headA;
        $b = $headB;

        while ($a !== $b) {
            $a = $a === null ? $headB : $a->next;
            $b = $b === null ? $headA : $b->next;
        }

        return $a;
    }
}

$solution = new Solution();

$shared = new ListNode(8, new ListNode(4, new ListNode(5)));
$headA = new ListNode(4, new ListNode(1, $shared));
$headB = new ListNode(5, new ListNode(6, new ListNode(1, $shared)));

$intersected = $solution->getIntersectionNode($headA, $headB);
echo ($intersected->val ?? 'No intersection') . PHP_EOL;

// Списки без пересечения.
$x = new ListNode(1, new ListNode(2));
$y = new ListNode(3, new ListNode(4));

$intersected = $solution->getIntersectionNode($x, $y);

echo ($intersected->val ?? 'No intersection') . PHP_EOL;
