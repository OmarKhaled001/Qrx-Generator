<?php

namespace Tests\Feature\Ui;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_switching_language_is_remembered_in_the_session(): void
    {
        $this->from('/dev/ui')->post('/locale/ar')->assertRedirect('/dev/ui')->assertSessionHas('locale', 'ar');

        $this->get('/dev/ui')->assertSee('dir="rtl"', false);
    }

    public function test_signed_in_users_keep_their_language(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->post('/locale/ar');

        $this->assertSame('ar', $user->fresh()->locale);
    }

    public function test_the_browser_language_is_used_by_default(): void
    {
        $this->get('/dev/ui', ['Accept-Language' => 'ar-EG,ar;q=0.9,en;q=0.5'])->assertSee('dir="rtl"', false);
        $this->get('/dev/ui', ['Accept-Language' => 'fr-FR'])->assertSee('dir="ltr"', false);
    }

    public function test_unsupported_languages_are_rejected(): void
    {
        $this->post('/locale/xx')->assertNotFound();
    }
}
