{{-- Marketing pages, the public scan page (minimal) and error pages. --}}
@props(['title' => null, 'description' => null, 'minimal' => false])
@php
    $links = array_filter([
        url('/#how-it-works') => __('How it works'),
        url('/#features') => __('Features'),
        url('/#pricing') => __('Pricing'),
        url('/#faq') => __('FAQ'),
        url('/#contact') => View::exists('landing.contact') ? __('Contact') : null,
    ]);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    @include('partials.head', ['title' => $title, 'description' => $description])
</head>
<body class="flex min-h-dvh flex-col">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-surface focus:px-4 focus:py-2 focus:shadow-pop">{{ __('Skip to content') }}</a>

    <header x-data="{ menu: false, scrolled: false }" x-on:scroll.window.passive="scrolled = window.scrollY > 8" x-on:keydown.escape.window="menu = false"
        :class="scrolled ? 'border-line bg-surface/85 shadow-xs backdrop-blur-lg' : 'border-transparent'"
        class="sticky top-0 z-30 border-b border-transparent transition-[background-color,border-color,box-shadow] duration-300">
        <div class="mx-auto flex h-16 w-full max-w-6xl items-center gap-4 px-4 sm:px-6">
            <a href="{{ url('/') }}" class="me-auto"><x-ui.logo /></a>
            @unless ($minimal)
                <nav class="hidden items-center gap-1 text-sm font-medium text-ink-muted md:flex" aria-label="{{ __('Main navigation') }}">
                    @foreach ($links as $href => $label)
                        <a href="{{ $href }}" class="rounded-md px-3 py-2 transition-colors hover:bg-sunken hover:text-ink">{{ $label }}</a>
                    @endforeach
                </nav>
            @endunless
            <div class="flex items-center gap-1">
                <x-ui.locale-switcher />
                <x-ui.theme-toggle />
            </div>
            @unless ($minimal)
                @auth
                    @if (Route::has('dashboard'))
                        <x-ui.button :href="route('dashboard')" size="sm" class="hidden sm:inline-flex">{{ __('Dashboard') }}</x-ui.button>
                    @endif
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="hidden rounded-md px-3 py-2 text-sm font-medium text-ink-muted transition-colors hover:bg-sunken hover:text-ink sm:block">{{ __('Log in') }}</a>
                    @endif
                    @if (Route::has('register'))
                        <x-ui.button :href="route('register')" size="sm" class="hidden sm:inline-flex">{{ __('Get started') }}</x-ui.button>
                    @endif
                @endauth
                <button type="button" class="grid size-9 place-items-center rounded-md text-ink-muted hover:bg-sunken md:hidden" x-on:click="menu = ! menu" :aria-expanded="menu" aria-controls="mobile-menu" aria-label="{{ __('Open menu') }}">
                    <x-heroicon-o-bars-3 x-show="! menu" class="size-5" aria-hidden="true" />
                    <x-heroicon-o-x-mark x-show="menu" x-cloak class="size-5" aria-hidden="true" />
                </button>
            @endunless
        </div>

        @unless ($minimal)
            <div id="mobile-menu" x-show="menu" x-cloak x-transition.origin.top class="border-t border-line bg-surface px-4 pt-2 pb-4 shadow-pop md:hidden">
                <nav class="grid gap-1 text-base font-medium" aria-label="{{ __('Main navigation') }}">
                    @foreach ($links as $href => $label)
                        <a href="{{ $href }}" x-on:click="menu = false" class="rounded-md px-3 py-2.5 text-ink hover:bg-sunken">{{ $label }}</a>
                    @endforeach
                </nav>
                <div class="mt-3 grid grid-cols-2 gap-2 border-t border-line pt-4">
                    @auth
                        @if (Route::has('dashboard'))
                            <x-ui.button :href="route('dashboard')" class="col-span-2">{{ __('Dashboard') }}</x-ui.button>
                        @endif
                    @else
                        @if (Route::has('login'))
                            <x-ui.button :href="route('login')" variant="secondary">{{ __('Log in') }}</x-ui.button>
                        @endif
                        @if (Route::has('register'))
                            <x-ui.button :href="route('register')">{{ __('Get started') }}</x-ui.button>
                        @endif
                    @endauth
                </div>
            </div>
        @endunless
    </header>

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    @if ($minimal)
        <footer class="py-8 text-center text-xs text-ink-subtle">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 rounded-md px-2 py-1 transition-colors hover:text-ink">
                {{ __('Made with :app', ['app' => config('app.name')]) }}
                <x-ui.logo mark class="h-4" />
            </a>
        </footer>
    @else
        <footer class="border-t border-line bg-surface">
            <div class="mx-auto grid w-full max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-[1.5fr_1fr_1fr]">
                <div>
                    <x-ui.logo class="text-ink" />
                    <p class="mt-4 max-w-xs text-sm text-ink-muted">{{ __('Dynamic QR codes you can edit, track and brand.') }}</p>
                </div>
                <nav aria-label="{{ __('Product') }}">
                    <p class="text-sm font-semibold text-ink">{{ __('Product') }}</p>
                    <ul class="mt-4 space-y-3 text-sm text-ink-muted">
                        @foreach ($links as $href => $label)
                            <li><a href="{{ $href }}" class="transition-colors hover:text-ink">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </nav>
                <nav aria-label="{{ __('Account') }}">
                    <p class="text-sm font-semibold text-ink">{{ __('Account') }}</p>
                    <ul class="mt-4 space-y-3 text-sm text-ink-muted">
                        @auth
                            @if (Route::has('dashboard'))<li><a href="{{ route('dashboard') }}" class="transition-colors hover:text-ink">{{ __('Dashboard') }}</a></li>@endif
                        @else
                            @if (Route::has('login'))<li><a href="{{ route('login') }}" class="transition-colors hover:text-ink">{{ __('Log in') }}</a></li>@endif
                            @if (Route::has('register'))<li><a href="{{ route('register') }}" class="transition-colors hover:text-ink">{{ __('Create an account') }}</a></li>@endif
                        @endauth
                    </ul>
                </nav>
            </div>
            <div class="border-t border-line">
                <p class="mx-auto w-full max-w-6xl px-4 py-6 text-sm text-ink-subtle sm:px-6">{{ __('© :year :app. All rights reserved.', ['year' => now()->year, 'app' => config('app.name')]) }}</p>
            </div>
        </footer>
    @endif

    <x-ui.toast-stack />
    @livewireScripts
</body>
</html>
