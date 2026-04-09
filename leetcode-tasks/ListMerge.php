<?php

class ListMerge
{
    protected function arrayToList(array $arr): ?ListNode
    {
        $tmp = new ListNode(0);
        $current = $tmp;

        foreach ($arr as $value) {
            $current->next = new ListNode($value);
            $current = $current->next;
        }

        return $tmp->next;
    }

    protected function listMerge(?object $list1, ?object $list2): ?ListNode
    {
        $tmp = new ListNode(0);
        $current = $tmp;

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

        if ($list1 !== null) {
            $current->next = $list1;
        } else {
            $current->next = $list2;
        }
        return $tmp->next;
    }

    public function merge(array|object|null $list1, array|object|null $list2): ?ListNode {
        $data1 = $list1;
        $data2 = $list2;

        if(is_array($list1)) {
            $data1 = $this->arrayToList($list1);
        }
        if(is_array($list2)) {
            $data2 = $this->arrayToList($list2);
        }

        return $this->listMerge($data1, $data2);
    }
}
