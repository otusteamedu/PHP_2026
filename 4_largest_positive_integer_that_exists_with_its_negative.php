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
    public function findMaxK(array $nums): int
    {
        $hash = [];
        foreach ($nums as $num) {
            $hash[$num] = true;
        }

        $result = -1;
        foreach ($nums as $num) {
            if ($num > 0 && isset($hash[-$num])) {
                $result = max($result, $num);
            }
        }

        return $result;
    }
}

$solution = new Solution();

$nums = [-1, 2, -3, 3];
$result = $solution->findMaxK($nums);
print_r($result);
echo PHP_EOL;
// Output: 3

$nums = [-1, 10, 6, 7, -7, 1];
$result = $solution->findMaxK($nums);
print_r($result);
echo PHP_EOL;
// Output: 7

$nums = [-10, 8, 6, 7, -2, -3];
$result = $solution->findMaxK($nums);
print_r($result);
echo PHP_EOL;
// Output: -1