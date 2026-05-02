<?php
/*
* https://leetcode.com/problems/largest-positive-integer-that-exists-with-its-negative/description/
*/
class Solution
{

    /**
     * @param int[] $nums
     * @return int
     */
    function findMaxK($nums)
    {
        $result = -1;
        $hash = [];
        foreach ($nums as $num) {
            $hash[$num] = true;
            if (isset($hash[-$num]) && $result < abs($num)) {
                $result = abs($num);
            }
        }
        return $result;
    }
}

$solution = new Solution();
$nums = [-1, 2, -3, 3];
$result = $solution->findMaxK($nums);
print_r($result);

$nums = [-1, 10, 6, 7, -7, 1];
$result = $solution->findMaxK($nums);
print_r($result);

$nums = [-10, 8, 6, 7, -2, -3];
$result = $solution->findMaxK($nums);
print_r($result);
