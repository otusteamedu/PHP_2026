<?php

declare(strict_types=1);

namespace Test\Unit\Application\Proxy\QualityChecker;

use App\Application\Proxy\QualityChecker\MaxCheeseChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MaxCheeseCheckerTest extends TestCase
{
    private MaxCheeseChecker $checker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new MaxCheeseChecker();
    }

    #[DataProvider('cases')]
    public function testSuccess(string $toCheck, bool $expected): void
    {
        self::assertEquals($expected, $this->checker->check($toCheck));
    }

    public static function cases(): array
    {
        return [
            ['сыр + хлеб', true],
            ['сыр + сыр + хлеб', true],
            ['сыр + сыр + сыр + хлеб', true],
            ['сыр + сыр + сыр + сыр', false],
            ['сыр + сыр + сыр + сыр + сыр', false],
        ];
    }
}
