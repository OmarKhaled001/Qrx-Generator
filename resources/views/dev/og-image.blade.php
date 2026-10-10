{{-- Open Graph card (1200x630), captured to public/brand/og-image-{locale}.png by scripts/brand-assets.mjs. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ \App\Support\Locales::dir() }}" class="dark">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        html, body { margin: 0; width: 1200px; height: 630px; overflow: hidden; }
        .og { position: relative; width: 1200px; height: 630px; overflow: hidden; color: #fff;
            background: radial-gradient(60rem 40rem at 85% 20%, rgb(117 67 233 / 0.45), transparent 60%),
                        radial-gradient(50rem 40rem at 10% 100%, rgb(0 165 171 / 0.35), transparent 60%),
                        #0b1220; }
        .og-grid { position: absolute; inset: 0; opacity: 0.12;
            background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px);
            background-size: 48px 48px; mask-image: radial-gradient(closest-side at 70% 50%, #000, transparent); }
    </style>
</head>
<body>
    <div class="og">
        <div class="og-grid"></div>
        <div class="relative flex h-full items-center gap-16 px-24">
            <div class="flex-1">
                <img src="{{ asset('brand/qrx-logo-dark.svg') }}" alt="" class="h-16">
                <p class="mt-14 text-6xl font-bold leading-tight tracking-tight">{{ __('Print once. Change it any time.') }}</p>
                <p class="mt-6 max-w-xl text-2xl leading-relaxed text-white/75">{{ __('Dynamic QR codes you can edit, track and brand.') }}</p>
            </div>
            <div class="relative shrink-0">
                <div class="absolute -inset-10 rounded-full bg-white/10 blur-3xl"></div>
                <img src="{{ asset('brand/qrx-icon.svg') }}" alt="" class="relative size-72 rotate-6 drop-shadow-2xl rtl:-rotate-6">
            </div>
        </div>
    </div>
</body>
</html>
