<?php

/*
 * https://leetcode.com/problems/intersection-of-two-arrays/description/
 */

class Solution
{
    /**
     * @param int[] $nums1
     * @param int[] $nums2
     * @return int[]
     */
    public function intersection(array $nums1, array $nums2): array
    {
        $hash = [];
        foreach ($nums1 as $num) {
            $hash[$num] = true;
        }

        $result = [];
        foreach ($nums2 as $num) {
            if (isset($hash[$num])) {
                $result[$num] = true;
            }
        }

        return array_keys($result);
    }
}

$solution = new Solution();

$nums1 = [1, 2, 2, 1];
$nums2 = [2, 2];
$result = $solution->intersection($nums1, $nums2);
print_r($result);
// Output: [2]

$nums1 = [4, 9, 5];
$nums2 = [9, 4, 9, 8, 4];
$result = $solution->intersection($nums1, $nums2);
print_r($result);
// Output: [9, 4]