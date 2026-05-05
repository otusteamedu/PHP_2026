<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase as BaseTestCase;

abstract class UnitTestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
