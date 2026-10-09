<?php

namespace Tests\Feature\Ui;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_brand_file_exists(): void
    {
        $files = [
            'favicon.ico', 'favicon.svg', 'apple-touch-icon.png', 'site.webmanifest',
            'brand/qrx-logo.svg', 'brand/qrx-logo-dark.svg', 'brand/qrx-logo-mono.svg', 'brand/qrx-icon.svg', 'brand/qrx-mark.svg',
            'brand/qrx-logo.png', 'brand/og-image-en.png', 'brand/og-image-ar.png',
        ];

        foreach ($files as $file) {
            $this->assertFileExists(public_path($file));
        }

        foreach (json_decode(file_get_contents(public_path('site.webmanifest')), true)['icons'] as $icon) {
            $this->assertFileExists(public_path($icon['src']));
        }
    }

    public function test_pages_link_the_icons_and_a_localised_share_image(): void
    {
        $this->get('/')->assertOk()->assertSee([
            'rel="apple-touch-icon"', 'rel="manifest"', 'brand/og-image-en.png', 'content="en_US"', 'summary_large_image',
        ], false);

        $this->withSession(['locale' => 'ar'])->get('/')->assertSee(['brand/og-image-ar.png', 'content="ar_AR"'], false);
    }

    public function test_the_admin_panel_shows_the_logo(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')
            ->assertOk()
            ->assertSee(['brand/qrx-logo.svg', 'brand/qrx-logo-dark.svg'], false);
    }

    public function test_the_share_image_page_is_rendered_per_language(): void
    {
        $this->get('/dev/og-image/ar')->assertOk()->assertSee('dir="rtl"', false);
        $this->get('/dev/og-image/fr')->assertNotFound();
    }
}
