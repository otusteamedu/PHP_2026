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
    public function smallerNumbersThanCurrent(array $nums): array
    {
        $result = [];
        $hash = array_count_values($nums);
        ksort($hash, SORT_NUMERIC);

        $total = 0;
        foreach ($hash as $value => $count) {
            $hash[$value] = $total;
            $total += $count;
        }

        foreach ($nums as $num) {
            $result[] = $hash[$num];
        }

        return $result;
    }
}

$solution = new Solution();

$nums = [8, 1, 2, 2, 3];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [4, 0, 1, 1, 3]

$nums = [6, 5, 4, 8];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [2, 1, 0, 3]

$nums = [7, 7, 7, 7];
$result = $solution->smallerNumbersThanCurrent($nums);
print_r($result);
// Output: [0, 0, 0, 0]