<?php

require __DIR__ . '/../vendor/autoload.php';

use App\ListNode;
use App\Solution;

$solution = new Solution();

echo "оба списка непустые: ";
$list1 = ListNode::linkedListFromArray([1, 2, 4]);
$list2 = ListNode::linkedListFromArray([1, 3, 4]);
Solution::printList($solution->mergeTwoLists($list1, $list2));
// Ожидается: [1,1,2,3,4,4]

echo "Оба списка пустые: ";
Solution::printList($solution->mergeTwoLists(null, null));
// Ожидается: []

echo "Первый список пустой: ";
$list2 = ListNode::linkedListFromArray([1, 3, 4]);
Solution::printList($solution->mergeTwoLists(null, $list2));
// Ожидается: [1,3,4]

echo "Второй список пустой: ";
$list1 = ListNode::linkedListFromArray([1, 2, 4]);
Solution::printList($solution->mergeTwoLists($list1, null));
// Ожидается: [1,2,4]

echo "По одному элементу в каждом: ";
$list1 = ListNode::linkedListFromArray([2]);
$list2 = ListNode::linkedListFromArray([1]);
Solution::printList($solution->mergeTwoLists($list1, $list2));
// Ожидается: [1,2]

echo "Списки разной длины: ";
$list1 = ListNode::linkedListFromArray([1, 5]);
$list2 = ListNode::linkedListFromArray([2, 3, 4, 6]);
Solution::printList($solution->mergeTwoLists($list1, $list2));
// Ожидается: [1,2,3,4,5,6]