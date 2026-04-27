<?php
/*
* https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
*/
class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    function smallerNumbersThanCurrent($nums) {
        $result = [];
        $hash = [];
        for ($i = 0; $i < count($nums); $i++) {
            $hash[$nums[$i]] = isset($hash[$nums[$i]]) ? $hash[$nums[$i]] + 1 : 1;
        }
        ksort($hash, SORT_NUMERIC);
        for ($i = 0; $i < count($nums); $i++) {
            $count = 0;
            foreach ($hash as $key => $value) {
                if ($key < $nums[$i]) {
                    $count += $value;
                } else {
                    break;
                }
            }
            $result[] = $count;
        }
        return $result;
    }
}

$solution = new Solution();
$nums = [8,1,2,2,3];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [4,0,1,1,3]

$nums = [6,5,4,8];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [2,1,0,3]

$nums = [7,7,7,7];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [0,0,0,0]