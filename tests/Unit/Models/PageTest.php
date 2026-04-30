<?php

namespace Tests\Unit\Models;

use App\Models\Page;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageTest extends TestCase
{
    #[Test]
    public function route_key_is_slug(): void
    {
        $this->assertSame('slug', (new Page)->getRouteKeyName());
    }

    #[Test]
    public function is_published_is_boolean_cast(): void
    {
        $page = new Page;
        $page->setRawAttributes(['is_published' => 0]);

        $this->assertFalse($page->is_published);
    }
}
