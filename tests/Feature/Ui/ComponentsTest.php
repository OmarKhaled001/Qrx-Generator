<?php

namespace Tests\Feature\Ui;

use Tests\TestCase;

class ComponentsTest extends TestCase
{
    public function test_the_component_gallery_renders_every_component(): void
    {
        $this->get('/dev/ui')
            ->assertOk()
            ->assertSee('dir="ltr"', false)
            ->assertSee(__('Create QR code'))
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-label="'.__('Language').'"', false);
    }

    public function test_the_gallery_renders_right_to_left_in_arabic(): void
    {
        $this->withSession(['locale' => 'ar'])
            ->get('/dev/ui')
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl">', false)
            ->assertSee('إنشاء رمز QR');
    }
}
