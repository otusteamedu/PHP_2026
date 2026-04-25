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
}
