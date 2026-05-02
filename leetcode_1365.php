<?php
/*
* https://leetcode.com/problems/how-many-numbers-are-smaller-than-the-current-number/description/
*/
class Solution
{

    /**
     * @param int[] $nums
     * @return int[]
     */
    function smallerNumbersThanCurrent(array $nums)
    {
        $result = [];
        $hash = [];
        $temp = $nums;
        sort($temp, SORT_NUMERIC);
        for ($i = 0; $i < count($temp); $i++) {
            if (!isset($hash[$temp[$i]])) $hash[$temp[$i]] = $i;
        }

        for ($i = 0; $i < count($nums); $i++) {
            $result[] = $hash[$nums[$i]];
        }
        return $result;
    }
}

$solution = new Solution();
$nums = [8, 1, 2, 2, 3];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [4,0,1,1,3]

$nums = [6, 5, 4, 8];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [2,1,0,3]

$nums = [7, 7, 7, 7];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [0,0,0,0]