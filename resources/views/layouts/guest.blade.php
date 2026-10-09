{{-- Sign-in, registration and password pages. --}}
@props(['title' => null, 'heading' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    @include('partials.head', ['title' => $title ?? $heading])
</head>
<body>
    <div class="relative flex min-h-dvh flex-col overflow-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-80 bg-linear-to-b from-brand-100/70 to-transparent dark:from-brand-950/60" aria-hidden="true"></div>

        <header class="relative flex items-center justify-between px-4 py-4 sm:px-8">
            <a href="{{ url('/') }}"><x-ui.logo /></a>
            <div class="flex items-center gap-1">
                <x-ui.locale-switcher />
                <x-ui.theme-toggle />
            </div>
        </header>

        <main id="main" class="relative flex flex-1 items-start justify-center px-4 pt-6 pb-16 sm:pt-12">
            <div class="w-full max-w-md">
                @if ($heading)
                    <div class="mb-6 text-center">
                        <h1 class="text-2xl font-semibold tracking-tight text-ink">{{ $heading }}</h1>
                        @if ($description)<p class="mt-2 text-sm text-ink-muted">{{ $description }}</p>@endif
                    </div>
                @endif
                <div class="rounded-lg border border-line bg-surface p-6 shadow-card sm:p-8">
                    {{ $slot }}
                </div>
                @isset($footer)
                    <div class="mt-6 text-center text-sm text-ink-muted">{{ $footer }}</div>
                @endisset
            </div>
        </main>
    </div>

    <x-ui.toast-stack />
</body>
</html>
