<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeAndStaticTest extends TestCase
{
    #[Test]
    public function home_and_about_respond_ok(): void
    {
        $this->get('/')->assertRedirect('/'.config('locale.default'));

        $this->get('/'.config('locale.testing').'/about')->assertOk();
        $this->get(route('static.info'))->assertOk();
    }
}
