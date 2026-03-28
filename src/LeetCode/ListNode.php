<?php

declare(strict_types=1);

namespace App\LeetCode;

class ListNode
{
    public function __construct(
        public mixed $val,
        public ?ListNode $next = null
    ) {}

    public static function linkedListToArray(?ListNode $head): array
    {
        $result = [];
        $current = $head;

        while ($current !== null) {
            $result[] = $current->val;
            $current = $current->next;
        }

        return $result;
    }

    public static function linkedListToNumber(?ListNode $head): int
    {
        $result = self::linkedListToArray($head);

        return (int) implode('', $result);
    }

    public static function linkedListFromArray(array $data): ?self
    {
        $n = count($data);
        if ($n === 0) {
            return null;
        }

        $initialItem = new self($data[$n -1], null);
        for ($i = $n - 2; $i >= 0; $i--) {
            $list = new self($data[$i], $initialItem);
            $initialItem = $list;
        }

        return $initialItem;
    }
}
