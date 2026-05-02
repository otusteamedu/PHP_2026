<?php
/*
* https://leetcode.com/problems/two-sum/description/
*/
class Solution
{

    /**
     * @param int[] $nums
     * @param int $target
     * @return int[]
     */
    function twoSum(array $nums, int $target): array
    {
        $result = [];
        $hash = [];

        for ($i = 0; $i < count($nums); $i++) {
            $diff = $target - $nums[$i];
            if (isset($hash[$diff])) {
                $result[] = $hash[$diff];
                $result[] = $i;
                return $result;
            }

            if (!isset($hash[$nums[$i]])) $hash[$nums[$i]] = $i;
        }
        return $result;
    }
}

$solution = new Solution();

$nums = [2, 7, 11, 15];
$target = 9;
$result = $solution->twoSum($nums, $target);
print_r($result);
// Output: [0,1]

$nums = [3, 2, 4];
$target = 6;
$result = $solution->twoSum($nums, $target);
print_r($result);
//Output: [1,2]

$nums = [3, 3];
$target = 6;
$result = $solution->twoSum($nums, $target);
print_r($result);
//Output: [0,1]