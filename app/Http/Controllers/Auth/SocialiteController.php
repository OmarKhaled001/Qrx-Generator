<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

/**
 * Sign in / sign up with Google or Facebook.
 */
class SocialiteController extends Controller
{
    public const PROVIDERS = ['google', 'facebook'];

    /**
     * Providers with credentials configured; only these get a button.
     *
     * @return list<string>
     */
    public static function enabledProviders(): array
    {
        return array_values(array_filter(self::PROVIDERS, fn (string $provider) => filled(config("services.{$provider}.client_id"))));
    }

    public function redirect(string $provider): SymfonyRedirect
    {
        abort_unless(in_array($provider, self::enabledProviders(), true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::enabledProviders(), true), 404);

        try {
            $social = Socialite::driver($provider)->user();
        } catch (Throwable) {
            return $this->failed(__('Signing in with :provider did not work. Please try again.', ['provider' => Str::title($provider)]));
        }

        $email = Str::lower((string) $social->getEmail());

        if ($email === '') {
            return $this->failed(__('Your :provider account did not share an email address, so we cannot create your account.', ['provider' => Str::title($provider)]));
        }

        $user = DB::transaction(function () use ($provider, $social, $email): User {
            // 1. Returning social user.
            $user = User::query()->where('provider', $provider)->where('provider_id', (string) $social->getId())->first();

            // 2. Existing account with the same (provider-verified) email: link it.
            $user ??= User::query()->where('email', $email)->first();

            // 3. New account.
            $user ??= new User([
                'name' => $social->getName() ?: Str::before($email, '@'),
                'email' => $email,
                'locale' => app()->getLocale(),
            ]);

            $user->forceFill([
                'provider' => $provider,
                'provider_id' => (string) $social->getId(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            return $user;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    private function failed(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['email' => $message]);
    }
}
