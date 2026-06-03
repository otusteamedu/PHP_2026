<?php

class ListNode
{
    public int $val = 0;
    public ?ListNode $next = null;
    function __construct(int $val)
    {
        $this->val = $val;
    }
}


class Solution
{
    /**
     * @param ListNode $headA
     * @param ListNode $headB
     * @return ListNode
     */
    function getIntersectionNode($headA, $headB): ?ListNode
    {
        $currentA = $headA;
        $currentB = $headB;

        while ($currentA !== $currentB) {
            $currentA = $currentA == null ? $headB : $currentA->next;
            $currentB = $currentB == null ? $headA : $currentB->next;
        }

        return $currentA;
    }
}

$solution = new Solution;
// example 1
$intersectionList = new ListNode(8);
$intersectionList->next = new ListNode(4);
$intersectionList->next->next =  new ListNode(5);

// $listA = [4, 1, 8, 4, 5];
$headA = new ListNode(4);
$headA->next = new ListNode(1);
$headA->next->next = $intersectionList;

// $listB = [5, 6, 1, 8, 4, 5];
$headB = new ListNode(5);
$headB->next = new ListNode(6);
$headB->next->next = new ListNode(1);
$headB->next->next->next = $intersectionList;

$intersected = $solution->getIntersectionNode($headA, $headB);
echo ($intersected->val ?? 'No intersection') . PHP_EOL;

// example 2
$intersectionList = new ListNode(2);
$intersectionList->next = new ListNode(4);

// $listA = [1,9,1,2,4];
$headA = new ListNode(1);
$headA->next = new ListNode(9);
$headA->next->next = new ListNode(1);
$headA->next->next->next = $intersectionList;

// listB = [3,2,4]
$headB = new ListNode(3);
$headB->next = $intersectionList;

$intersected = $solution->getIntersectionNode($headA, $headB);
echo ($intersected->val ?? 'No intersection') . PHP_EOL;


// example 3
// listA = [2,6,4]
$headA = new ListNode(2);
$headA->next = new ListNode(6);
$headA->next->next = new ListNode(4);

// listB = [1,5]
$headB = new ListNode(1);
$headB->next = new ListNode(6);

$intersected = $solution->getIntersectionNode($headA, $headB);
echo ($intersected->val ?? 'No intersection') . PHP_EOL;
