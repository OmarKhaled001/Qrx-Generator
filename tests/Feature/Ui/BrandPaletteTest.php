<?php

namespace Tests\Feature\Ui;

use App\Models\User;
use App\Support\BrandPalette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandPaletteTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_admin_palette_is_read_from_the_design_tokens(): void
    {
        $brand = BrandPalette::get('brand');

        $this->assertSame([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], array_keys($brand));
        $this->assertSame('oklch(0.545 0.107 202)', $brand[600]);
        $this->assertCount(11, BrandPalette::get('neutral'));
    }

    public function test_the_admin_panel_uses_the_shared_theme_and_local_fonts(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertOk()
            ->assertSee('build/assets/theme-', false)
            ->assertDontSee('fonts.bunny.net', false)
            ->assertDontSee('fonts.googleapis.com', false);
    }
}
