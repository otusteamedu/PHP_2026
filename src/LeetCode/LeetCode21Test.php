<?php

namespace App\LeetCode;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LeetCode21Test extends TestCase
{
    #[DataProvider('toListsDP')]
    public function testMergeTwoLists(?ListNode $list1 = null, ?ListNode $list2 = null, ?ListNode $expected = null)
    {
        $results = LeetCode21::mergeTwoLists($list1, $list2);

        $this->assertEquals($expected, $results);
    }

    public static function toListsDP(): array
    {
        return [
            [
                ListNode::linkedListFromArray([1,2,4]),
                ListNode::linkedListFromArray([1,3,4]),
                ListNode::linkedListFromArray([1,1,2,3,4,4]),
            ],
            [
                ListNode::linkedListFromArray([]),
                ListNode::linkedListFromArray([0]),
                ListNode::linkedListFromArray([0]),
            ],
            [
                ListNode::linkedListFromArray([]),
                ListNode::linkedListFromArray([]),
                ListNode::linkedListFromArray([]),
            ],
        ];
    }
}
