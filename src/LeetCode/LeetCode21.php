<?php

declare(strict_types=1);

namespace App\LeetCode;

class LeetCode21
{

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    public static function mergeTwoLists(?ListNode $list1 = null, ?ListNode $list2 = null): ?ListNode
    {
        $head = null;

        while ($list1 !== null && $list2 !== null) {
            $l = $list1->val;
            $r = $list2->val;

            if ($l <= $r) {
                $node = new ListNode($l);
                $head = self::updateHead($head, $node);
                $list1 = $list1->next;
            } else {
                $node = new ListNode($r);
                $head = self::updateHead($head, $node);
                $list2 = $list2->next;
            }
        }

        // Дописываем все что осталось из первого
        while ($list1 !== null) {
            $node = new ListNode($list1->val);
            $head = self::updateHead($head, $node);
            $list1 = $list1->next;
        }

        // Дописываем все что осталось из второго
        while ($list2 !== null) {
            $node = new ListNode($list2->val);
            $head = self::updateHead($head, $node);
            $list2 = $list2->next;
        }

        return $head;
    }

    /**
     * @param ListNode|null $head
     * @param ListNode $node
     * @return ListNode
     */
    public static function updateHead(?ListNode $head, ListNode $node): ListNode
    {
        if ($head === null) {
            return $node;
        }

        $current = $head;
        while ($current->next !== null) {
            $current = $current->next;
        }

        $current->next = $node;

        return $head;
    }
}
