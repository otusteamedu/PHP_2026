<?php

declare(strict_types=1);

/**
 * Given two integer arrays nums1 and nums2, return an array of their intersection.
 * Each element in the result must be unique, and you may return the result in any order.
 *
 * Example 1:
 * Input: nums1 = [1,2,2,1], nums2 = [2,2]
 * Output: [2]
 *
 * Example 2:
 * Input: nums1 = [4,9,5], nums2 = [9,4,9,8,4]
 * Output: [9,4]
 * Explanation: [4,9] is also accepted.
 */
class Solution {

    /**
     * Сложность O(n + m) => O(n)
     *
     * @param Integer[] $nums1
     * @param Integer[] $nums2
     * @return Integer[]
     */
    function intersection($nums1, $nums2): array
    {
        $result = [];
        $hash = [];
        foreach ($nums1 as $num) {
            $hash[$num] = true;
        }

        foreach ($nums2 as $num) {
            if (isset($hash[$num])) {
                $result[$num] = $num;
            }
        }

        return $result;
    }
}
