<?php

declare(strict_types=1);

namespace App\Storage;

trait ConditionsTrait
{
    private function matches(array $conditions, array $params): bool
    {
        foreach ($conditions as $k => $v) {
            if (($params[$k] ?? null) !== (string) $v) {
                return false;
            }
        }
        return true;
    }
}
