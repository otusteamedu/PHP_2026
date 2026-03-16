<?php

declare(strict_types=1);

namespace App;

class Solution {

    /**
     * @param ListNode|null $list1
     * @param ListNode|null $list2
     * @return ListNode|null
     */
    public function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode {
        $dummy = new ListNode();
        $current = $dummy;

        while ($list1 !== null && $list2 !== null) {

            if ($list1->val <= $list2->val) {
                $current->next = $list1;
                $list1 = $list1->next;
            } else {
                $current->next = $list2;
                $list2 = $list2->next;
            }

            $current = $current->next;
        }

        $current->next = $list1 ?? $list2;

        return $dummy->next;
    }

    public static function printList(?ListNode $node): void
    {
        $result = [];

        while ($node !== null) {
            $result[] = $node->val;
            $node = $node->next;
        }

        echo '[' . implode(',', $result) . ']' . PHP_EOL;
    }
}
