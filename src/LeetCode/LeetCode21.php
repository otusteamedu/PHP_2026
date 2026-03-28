<?php

declare(strict_types=1);

namespace App\LeetCode;

class LeetCode21
{
    public static function mergeTwoLists(?ListNode $list1 = null, ?ListNode $list2 = null): ?ListNode
    {
        $root = new ListNode(0);
        $current = $root;

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

        // Дописываем все что осталось от первого или второго
        $current->next = $list1 ?? $list2;

        return $root->next;
    }
}
