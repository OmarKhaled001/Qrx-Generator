<?php

namespace Tests\Feature;

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

    private function fakeProviderUser(string $provider, string $id, string $email): void
    {
        $socialUser = (new SocialiteUser)->map(['id' => $id, 'name' => 'Social User', 'email' => $email]);

        $driver = Mockery::mock(Provider::class);
        $driver->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);
    }

    public function test_first_social_login_creates_a_user_without_address_details(): void
    {
        $this->fakeProviderUser('google', 'google-123', 'social@example.com');

        $this->get(route('socialite.callback', 'google'))
            ->assertRedirect(route('dashboard'));

        $user = User::where('email', 'social@example.com')->firstOrFail();
        $this->assertSame('google', $user->provider);
        $this->assertSame('google-123', $user->provider_id);
        $this->assertNull($user->phone);
        $this->assertAuthenticatedAs($user);
    }

    public function test_returning_social_user_is_logged_into_the_same_account(): void
    {
        $this->fakeProviderUser('facebook', 'fb-456', 'returning@example.com');

        $this->get(route('socialite.callback', 'facebook'))->assertRedirect(route('dashboard'));
        auth()->logout();
        $this->get(route('socialite.callback', 'facebook'))->assertRedirect(route('dashboard'));

        $this->assertSame(1, User::where('provider_id', 'fb-456')->count());
        $this->assertAuthenticated();
    }

    public function test_unsupported_providers_are_not_routed(): void
    {
        $this->get('/auth/twitter/redirect')->assertNotFound();
        $this->get('/auth/twitter/callback')->assertNotFound();
    }
}
