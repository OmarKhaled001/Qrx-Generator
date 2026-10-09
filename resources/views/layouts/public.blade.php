{{-- Marketing pages, the public scan page (minimal) and error pages. --}}
@props(['title' => null, 'description' => null, 'minimal' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    @include('partials.head', ['title' => $title, 'description' => $description])
</head>
<body class="flex min-h-dvh flex-col">
    <header class="sticky top-0 z-30 border-b border-line bg-surface/85 backdrop-blur">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center gap-4 px-4 sm:px-6">
            <a href="{{ url('/') }}" class="me-auto"><x-ui.logo /></a>
            @unless ($minimal)
                <nav class="hidden items-center gap-6 text-sm font-medium text-ink-muted md:flex" aria-label="{{ __('Main navigation') }}">
                    <a href="{{ url('/#features') }}" class="hover:text-ink">{{ __('Features') }}</a>
                    <a href="{{ url('/#pricing') }}" class="hover:text-ink">{{ __('Pricing') }}</a>
                    @if (Route::has('contact.store'))
                        <a href="{{ url('/#contact') }}" class="hover:text-ink">{{ __('Contact') }}</a>
                    @endif
                </nav>
            @endunless
            <div class="flex items-center gap-1">
                <x-ui.locale-switcher />
                <x-ui.theme-toggle />
            </div>
            @unless ($minimal)
                @auth
                    @if (Route::has('dashboard'))
                        <x-ui.button :href="route('dashboard')" size="sm">{{ __('Dashboard') }}</x-ui.button>
                    @endif
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="hidden text-sm font-medium text-ink-muted hover:text-ink sm:block">{{ __('Log in') }}</a>
                    @endif
                    @if (Route::has('register'))
                        <x-ui.button :href="route('register')" size="sm">{{ __('Get started') }}</x-ui.button>
                    @endif
                @endauth
            @endunless
        </div>
    </header>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <footer class="border-t border-line bg-surface">
        <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-8 text-sm text-ink-subtle sm:px-6">
            <x-ui.logo />
            <p>{{ __('© :year :app. All rights reserved.', ['year' => now()->year, 'app' => config('app.name')]) }}</p>
        </div>
    </footer>

    <x-ui.toast-stack />
</body>
</html>
