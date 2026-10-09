{{-- Standalone: no session, forms or Livewire, so it renders even when the app is failing. --}}
@props(['code', 'heading', 'message'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>{{ $heading }} · {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="grid min-h-dvh place-items-center bg-page px-4 text-ink">
    <main class="max-w-md text-center">
        <a href="{{ url('/') }}" class="inline-block"><x-ui.logo /></a>
        <p class="mt-10 text-sm font-semibold tracking-widest text-primary-ink tabular-nums">{{ $code }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight">{{ $heading }}</h1>
        <p class="mt-3 text-ink-muted">{{ $message }}</p>
        <a href="{{ url('/') }}" class="mt-8 inline-flex h-10 items-center rounded-md bg-primary px-4 text-sm font-medium text-on-primary hover:bg-primary-hover">{{ __('Back to home') }}</a>
    </main>
</body>
</html>
