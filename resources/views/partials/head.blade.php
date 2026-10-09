@php($title ??= null)
@php($description ??= null)
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
@php($description ??= __('Dynamic QR codes you can edit, track and brand.'))
<meta name="description" content="{{ $description }}">
<meta name="theme-color" content="#00858e">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $title ?? __('Print once. Change it any time.') }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:locale" content="{{ \App\Support\Locales::ogLocale() }}">
<meta property="og:image" content="{{ asset('brand/og-image-'.app()->getLocale().'.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<script>
    // Apply the saved or system theme before first paint (no flash).
    (() => {
        let theme = null;
        try { theme = localStorage.getItem('qrx-theme'); } catch {}
        const dark = theme ? theme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', dark);
    })();
</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
