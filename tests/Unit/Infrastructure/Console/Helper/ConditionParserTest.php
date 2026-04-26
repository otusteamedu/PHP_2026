<?php

namespace Test\Unit\Infrastructure\Console\Helper;

use App\Infrastructure\Console\Helper\ConditionParser;
use PHPUnit\Framework\TestCase;

class ConditionParserTest extends TestCase
{
    public function testSuccess(): void
    {
        $expected = [
            'param1' => 10,
            'param2' => 20,
        ];

        $result = new ConditionParser()->parseConditions('param1=10,param2=20');
        $this->assertEquals($expected, $result);
    }
}
