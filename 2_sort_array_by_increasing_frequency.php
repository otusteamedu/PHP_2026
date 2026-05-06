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
    public function frequencySort(array $nums): array
    {
        $hash = [];
        foreach ($nums as $num) {
            $hash[$num] = ($hash[$num] ?? 0) + 1;
        }

        usort($nums, function (int $a, int $b) use ($hash): int {
            if ($hash[$a] !== $hash[$b]) {
                return $hash[$a] - $hash[$b];
            }
            return $b - $a;
        });

        return $nums;
    }
}

$solution = new Solution();

$nums = [1, 1, 2, 2, 2, 3];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [3, 1, 1, 2, 2, 2]

$nums = [2, 3, 1, 3, 2];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [1, 3, 3, 2, 2]

$nums = [-1, 1, -6, 4, 5, -6, 1, 4, 1];
$result = $solution->frequencySort($nums);
print_r($result);
// Output: [5, -1, 4, 4, -6, -6, 1, 1, 1]