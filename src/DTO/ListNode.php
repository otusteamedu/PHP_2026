<?php

declare(strict_types=1);

namespace evgeny87\LeetCode\DTO;

/**
 * Класс-узел связного списка.
 * Свойства оставлены изменяемыми для поддержки алгоритма In-Place.
 */
final class ListNode
{
    public function __construct(
        public int $val = 0,
        public ?ListNode $next = null
    ) {
    }
 }
