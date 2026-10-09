@php($title ??= null)
@php($description ??= null)
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
<meta name="description" content="{{ $description ?? __('Dynamic QR codes you can edit, track and brand.') }}">
<meta name="theme-color" content="#0e7a8a">
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
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
