<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google.client_id' => 'google-client', 'services.google.client_secret' => 'secret', 'services.facebook.client_id' => null]);
    }

    private function fakeProviderUser(string $id, ?string $email, string $name = 'Sara Ali'): void
    {
        $socialUser = (new SocialiteUser)->map(['id' => $id, 'name' => $name, 'email' => $email]);

        $driver = Mockery::mock(Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($driver);
    }

    public function test_buttons_show_only_for_configured_providers(): void
    {
        $this->get('/login')->assertSee(__('Continue with :provider', ['provider' => 'Google']))->assertDontSee('Facebook');
        $this->get('/auth/facebook/redirect')->assertNotFound();
        $this->get('/auth/twitter/redirect')->assertNotFound();
    }

    public function test_first_sign_in_creates_a_verified_account(): void
    {
        $this->fakeProviderUser('g-1', 'sara@example.com');

        $this->get('/auth/google/callback')->assertRedirect('/dashboard');

        $user = User::query()->where('email', 'sara@example.com')->sole();
        $this->assertSame(['google', 'g-1'], [$user->provider, $user->provider_id]);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNull($user->password);
        $this->assertAuthenticatedAs($user);
        $this->get('/dashboard')->assertOk();
    }

    public function test_returning_users_sign_into_the_same_account(): void
    {
        $this->fakeProviderUser('g-2', 'back@example.com');

        $this->get('/auth/google/callback');
        auth()->logout();
        $this->get('/auth/google/callback');

        $this->assertSame(1, User::query()->where('provider_id', 'g-2')->count());
        $this->assertAuthenticated();
    }

    public function test_an_existing_email_account_is_linked_not_duplicated(): void
    {
        $existing = User::factory()->create(['email' => 'owner@example.com']);
        $this->fakeProviderUser('g-3', 'Owner@Example.com');

        $this->get('/auth/google/callback')->assertRedirect('/dashboard');

        $this->assertSame(1, User::count());
        $this->assertSame('g-3', $existing->fresh()->provider_id);
        $this->assertAuthenticatedAs($existing);
    }

    public function test_accounts_without_an_email_are_refused(): void
    {
        $this->fakeProviderUser('g-4', null);

        $this->get('/auth/google/callback')->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertSame(0, User::count());
    }

    public function test_social_only_users_can_delete_their_account_by_typing_their_email(): void
    {
        $this->fakeProviderUser('g-5', 'leaving@example.com');
        $this->get('/auth/google/callback');
        $user = User::query()->where('email', 'leaving@example.com')->sole();

        $this->delete('/profile', ['confirm_email' => 'wrong@example.com'])->assertSessionHasErrorsIn('userDeletion', 'confirm_email');
        $this->delete('/profile', ['confirm_email' => 'leaving@example.com'])->assertRedirect('/');

        $this->assertNull($user->fresh());
    }
}
