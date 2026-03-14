<?php


//Definition for a singly-linked list.
class ListNode
{
    public $val = 0;
    public $next = null;
    function __construct($val = 0, $next = null)
    {
        $this->val = $val;
        $this->next = $next;
    }
}

class Solution
{

    /**
     * @param ListNode $list1
     * @param ListNode $list2
     * @return ListNode
     */
    function mergeTwoLists($list1, $list2)
    {
        if (is_null($list1)) return $list2;
        if (is_null($list2)) return $list1;

        if ($list1->val < $list2->val) {
            $list1->next = $this->mergeTwoLists($list1->next, $list2);
            return $list1;
        } else {
            $list2->next = $this->mergeTwoLists($list1, $list2->next);
            return $list2;
        }
    }
}

/**
 * @param ListNode $list
 */
function printList(?ListNode $list)
{
    $values = [];

    while ($list !== null) {
        $values[] = $list->val;
        $list = $list->next;
    }

    echo '[' . implode(', ', $values) . ']' . PHP_EOL;
}
// Example 1:
$list1 = new ListNode(1);
$list1->next = new ListNode(2);
$list1->next->next = new ListNode(4);

$list2 = new ListNode(1);
$list2->next = new ListNode(3);
$list2->next->next = new ListNode(4);

$mergeList = (new Solution)->mergeTwoLists($list1, $list2);
echo 'Example 1' . PHP_EOL;
echo 'List 1: ';
printList($list1);
echo 'List 2: ';
printList($list2);
echo 'Merge list: ';
printList($mergeList);
echo PHP_EOL;


// Example 2:
$list1 = null;
$list2 = null;

$mergeList = (new Solution)->mergeTwoLists($list1, $list2);
echo 'Example 2' . PHP_EOL;
echo 'List 1: ';
printList($list1);
echo 'List 2: ';
printList($list2);
echo 'Merge list: ';
printList($mergeList);
echo PHP_EOL;

// Example 3:
$list1 = null;
$list2 = new ListNode(0);

$mergeList = (new Solution)->mergeTwoLists($list1, $list2);
echo 'Example 3' . PHP_EOL;
echo 'List 1: ';
printList($list1);
echo 'List 2: ';
printList($list2);
echo 'Merge list: ';
printList($mergeList);
echo PHP_EOL;
