<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

class ConditionParser
{
    /**
     * @return array<string, int>
     */
    public function parseConditions(string $conditionsString): array
    {
        $conditions = [];
        $pairs = explode(',', $conditionsString);

        foreach ($pairs as $pair) {
            $parts = explode('=', $pair);
            if (count($parts) !== 2) {
                throw new \InvalidArgumentException(sprintf('Неверный формат условия: "%s". Ожидается key=value', $pair));
            }

            [$key, $value] = $parts;
            $key = trim($key);
            $value = trim($value);

            if ($key === '' || $value === '') {
                throw new \InvalidArgumentException(sprintf('Ключ или значение не могут быть пустыми в условии: "%s"', $pair));
            }

            if (!is_numeric($value)) {
                throw new \InvalidArgumentException(sprintf('Значение условия "%s" должно быть числом.', $key));
            }

            $conditions[$key] = (int) $value;
        }

        return $conditions;
    }
}
