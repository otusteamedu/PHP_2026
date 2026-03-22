<?php

declare(strict_types=1);

namespace evgeny87\LeetCode\Exceptions;

use Exception;

/**
 * Кастомное исключение для обозначения пустых списков.
 * Соответствует архитектурному слою Exceptions.
 */
final class ListEmptyException extends Exception
{
}
