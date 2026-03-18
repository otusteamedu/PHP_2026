<?php

declare(strict_types=1);

class Solution {

    function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ?ListNode
    {
        if (!$list2 || ($list1 && $list1->val < $list2->val)) {
            $result = $list1;
            $toWhile = $list2;
        } else {
            $result = $list2;
            $toWhile = $list1;
        }

        $prevNode = $result;
        while ($toWhile) {
            $next = $prevNode->next;
            if (!$next || $toWhile->val < $next->val) {
                $toSetVal = $toWhile->val;
                $prevNode->next = new ListNode($toWhile->val, $next);
                $toWhile = $toWhile->next;
            }

            $prevNode = $prevNode->next;
        }

        return $result;
    }
}

class ListNode {
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null) {
        $this->val = $val;
        $this->next = $next;
    }
}
