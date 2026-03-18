<?php

declare(strict_types=1);

class Solution {

    function mergeTwoLists(?ListNode $list1, ?ListNode $list2): ListNode
    {
        if (!$list2 || ($list1 && $list1->val < $list2->val)) {
            $toSetVal = $list1->val;
            $list1 = $list1->next;
        } else {
            $toSetVal = $list2->val;
            $list2 = $list2->next;
        }

        $resultNode = new ListNode($toSetVal);
        $prevNode = $resultNode;
        while ($list1) {
            if (!$list2 || $list1->val < $list2->val) {
                $toSetVal = $list1->val;
                $list1 = $list1->next;
            } else {
                $toSetVal = $list2->val;
                $list2 = $list2->next;
            }
            $prevNode->next = new ListNode($toSetVal);
            $prevNode = $prevNode->next;
        }

        while ($list2) {
            $prevNode->next = new ListNode($list2->val);
            $prevNode = $prevNode->next;
            $list2 = $list2->next;
        }

        return $resultNode;
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
