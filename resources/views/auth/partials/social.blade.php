@php($providers = \App\Http\Controllers\Auth\SocialiteController::enabledProviders())
@if ($providers)
    <div class="my-6 flex items-center gap-3 text-xs text-ink-subtle">
        <span class="h-px flex-1 bg-line"></span>{{ __('or continue with') }}<span class="h-px flex-1 bg-line"></span>
    </div>
    <div class="grid gap-3">
        @foreach ($providers as $provider)
            <a href="{{ route('socialite.redirect', $provider) }}" class="flex h-10 items-center justify-center gap-3 rounded-md border border-line-strong bg-surface text-sm font-medium text-ink transition hover:bg-sunken">
                @if ($provider === 'google')
                    <svg viewBox="0 0 24 24" class="size-5" aria-hidden="true"><path fill="#4285F4" d="M22.6 12.2c0-.8-.1-1.5-.2-2.2H12v4.2h5.9a5 5 0 0 1-2.2 3.3v2.7h3.6c2.1-1.9 3.3-4.8 3.3-8z"/><path fill="#34A853" d="M12 23c3 0 5.5-1 7.3-2.7l-3.6-2.8c-1 .7-2.2 1.1-3.7 1.1a6.4 6.4 0 0 1-6-4.4H2.3v2.8A11 11 0 0 0 12 23z"/><path fill="#FBBC05" d="M6 14.2a6.6 6.6 0 0 1 0-4.3V7.1H2.3a11 11 0 0 0 0 9.9L6 14.2z"/><path fill="#EA4335" d="M12 5.4c1.6 0 3.1.6 4.2 1.6l3.2-3.2A11 11 0 0 0 2.3 7.1L6 9.9a6.4 6.4 0 0 1 6-4.5z"/></svg>
                @else
                    <svg viewBox="0 0 24 24" class="size-5" aria-hidden="true"><path fill="#1877F2" d="M24 12a12 12 0 1 0-13.9 11.9v-8.4H7.1V12h3V9.4c0-3 1.8-4.7 4.6-4.7 1.3 0 2.7.2 2.7.2v3h-1.5c-1.5 0-2 .9-2 1.9V12h3.4l-.5 3.5h-2.9v8.4A12 12 0 0 0 24 12z"/></svg>
                @endif
                {{ __('Continue with :provider', ['provider' => Str::title($provider)]) }}
            </a>
        @endforeach
    </div>
@endif
