<?php
/*
* https://leetcode.com/problems/intersection-of-two-arrays/description/
*/
class Solution {

    /**
     * @param int[] $nums1
     * @param int[] $nums2
     * @return int[]
     */
    function intersection($nums1, $nums2) {
        $hash = [];
        $result = [];
        for ($i = 0; $i < count($nums1); $i++) {
            $hash[$nums1[$i]] = true;
        }
        for ($i = 0; $i < count($nums2); $i++) {
            if (isset($hash[$nums2[$i]])) {
                $result[$nums2[$i]] = $nums2[$i];
            }
        }
        return $result;
    }
}

$solution = new Solution();

$nums1 = [1,2,2,1];
$nums2 = [2,2];
$result = $solution->intersection($nums1, $nums2);
print_r($result);
// Output: [2]

$nums1 = [4,9,5];
$nums2 = [9,4,9,8,4];
$result = $solution->intersection($nums1, $nums2);
print_r($result);
// Output: [9,4]