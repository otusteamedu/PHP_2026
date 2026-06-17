<?php

declare (strict_types = 1);

namespace Tests\Unit\Container;

use App\HTTP\Controllers\EmailController;
use App\Infrastructure\Container\Container;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{  
    public function test_email_controller_is_created(): void
    {
        $container = new Container();

        $controller = $container->emailController();

        $this->assertInstanceOf(
            EmailController::class,
            $controller
        );
    }
}
