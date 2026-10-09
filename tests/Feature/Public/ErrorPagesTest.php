<?php

namespace Tests\Feature\Public;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_404_is_branded(): void
    {
        $this->get('/no-such-page')->assertNotFound()->assertSee(__('Page not found'))->assertSee(config('app.name'));
    }

    public function test_404_is_translated(): void
    {
        $this->withSession(['locale' => 'ar'])->get('/no-such-page')->assertNotFound()->assertSee('dir="rtl"', false)->assertSee('الصفحة غير موجودة');
    }

    public function test_error_views_render_without_a_session(): void
    {
        foreach (['403', '419', '429', '500', '503'] as $code) {
            $this->assertStringContainsString($code, view("errors.{$code}", ['exception' => null])->render());
        }
    }
}
