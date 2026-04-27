<?php
/*
* https://leetcode.com/problems/sort-array-by-increasing-frequency/description/
*/
class Solution
{

    /**
     * @param int[] $nums
     * @return int[]
     */
    function frequencySort(array $nums): array
    {
        $result = [];
        $hash = [];
        for ($i = 0; $i < count($nums); $i++) {
            $hash[$nums[$i]] = isset($hash[$nums[$i]]) ? $hash[$nums[$i]] + 1 : 1;
        }
        krsort($hash, SORT_NUMERIC);
        asort($hash, SORT_NUMERIC);
        foreach ($hash as $num => $count) {
            for ($j = 0; $j < $count; $j++) {
                $result[] = $num;
            }
        }
        return $result;
    }
}

$solution = new Solution();


$nums = [1,1,2,2,2,3];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [3,1,1,2,2,2]

$nums = [2, 3, 1, 3, 2];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [1,3,3,2,2]

$nums = [-1, 1, -6, 4, 5, -6, 1, 4, 1];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [5,-1,4,4,-6,-6,1,1,1]