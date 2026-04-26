<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert;

final readonly class Conditions
{
    /**
     * @param Param[] $params
     */
    public function __construct(
        public array $params,
    ) {
    }

    /**
     * @param array<string, int> $params
     */
    public static function create(array $params): self
    {
        Assert::notEmpty($params);

        $paramsToSet = [];
        foreach ($params as $name => $value) {
            $paramsToSet[] = Param::create($name, $value);
        }
        return new self($paramsToSet);
    }

    public static function fromKey(string $key): self
    {
        Assert::notEmpty($key);

        $parts = explode(':', $key);
        $pairs = array_chunk($parts, 2);

        $paramsToSet = [];
        foreach ($pairs as [$name, $value]) {
            $paramsToSet[] = Param::create($name, (int) $value);
        }
        return new self($paramsToSet);
    }

    public function getSortedParams(): array
    {
        $params = $this->params;
        usort($params, fn(Param $a, Param $b) => $a->name <=> $b->name);
        return $params;
    }

    public function getSortedKey(): string
    {
        $keys = array_map(fn(Param $param) => "{$param->name}:{$param->value}", $this->getSortedParams());
        return implode(':', $keys);
    }

    public function getAllPossibleSortedKeys(): array
    {
        $keys = [];
        $params = $this->getSortedParams();
        $count = count($params);

        $generate = function (int $index, array $current) use (&$generate, &$keys, $params, $count): void {
            if ($index === $count) {
                if (!empty($current)) {
                    $subsetKeys = array_map(fn(Param $param) => "{$param->name}:{$param->value}", $current);
                    $keys[] = implode(':', $subsetKeys);
                }
                return;
            }

            // Вариант без текущего параметра
            $generate($index + 1, $current);

            // Вариант с текущим параметром
            $current[] = $params[$index];
            $generate($index + 1, $current);
        };

        $generate(0, []);

        return $keys;
    }
}
