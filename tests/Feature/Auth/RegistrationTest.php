<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function form(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+20 100 000 0000',
            'country' => 'Egypt',
            'city' => 'Cairo',
            'area' => 'Zamalek',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk()->assertSee(__('Create your account'));
    }

    public function test_new_users_can_register_and_must_verify_their_email(): void
    {
        Notification::fake();

        $this->post('/register', $this->form())->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $user = User::sole();
        $this->assertSame('Cairo', $user->city);
        $this->assertSame('en', $user->locale);
        $this->assertFalse($user->is_admin);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get('/dashboard')->assertRedirect(route('verification.notice'));
    }

    public function test_contact_details_are_required(): void
    {
        $this->post('/register', $this->form(['phone' => '', 'city' => '']))->assertSessionHasErrors(['phone', 'city']);
        $this->assertGuest();
    }

    public function test_registering_in_arabic_saves_the_language(): void
    {
        $this->withSession(['locale' => 'ar'])->post('/register', $this->form());

        $this->assertSame('ar', User::sole()->locale);
    }
}
