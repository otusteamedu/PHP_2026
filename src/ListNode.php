<?php

declare(strict_types=1);

namespace App;

final class ListNode
{
    public int $val;
    public ?ListNode $next;

    public function __construct(int $val = 0, ?self $next = null)
    {
        $this->val = $val;
        $this->next = $next;
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