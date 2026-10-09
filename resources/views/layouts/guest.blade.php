{{-- Sign-in, registration and password pages. --}}
@props(['title' => null, 'heading' => null, 'description' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ \App\Support\Locales::dir() }}">
<head>
    @include('partials.head', ['title' => $title ?? $heading])
</head>
<body>
    <div class="flex min-h-dvh">
        <div class="relative flex min-w-0 flex-1 flex-col">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-96 bg-glow" aria-hidden="true"></div>

            <header class="relative flex items-center justify-between px-4 py-4 sm:px-8">
                <a href="{{ url('/') }}"><x-ui.logo /></a>
                <div class="flex items-center gap-1">
                    <x-ui.locale-switcher />
                    <x-ui.theme-toggle />
                </div>
            </header>

            <main id="main" class="relative flex flex-1 items-start justify-center px-4 pt-6 pb-16 sm:items-center sm:pt-0">
                <div class="w-full max-w-md">
                    @if ($heading)
                        <div class="mb-8 text-center">
                            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">{{ $heading }}</h1>
                            @if ($description)<p class="mt-2 text-ink-muted">{{ $description }}</p>@endif
                        </div>
                    @endif
                    <div class="rounded-xl border border-line bg-surface p-6 shadow-raised inset-shadow-highlight sm:p-8">
                        {{ $slot }}
                    </div>
                    @isset($footer)
                        <div class="mt-6 text-center text-sm text-ink-muted">{{ $footer }}</div>
                    @endisset
                </div>
            </main>
        </div>

        {{-- Brand panel (large screens): what the product does, no invented social proof. --}}
        <aside class="relative hidden w-[42%] max-w-2xl overflow-hidden bg-brand-gradient p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16" aria-hidden="true">
            <div class="absolute inset-0 opacity-15 [background-image:linear-gradient(#fff_1px,transparent_1px),linear-gradient(90deg,#fff_1px,transparent_1px)] [background-size:44px_44px] [mask-image:radial-gradient(closest-side,#000,transparent)]"></div>
            <div class="relative inline-flex w-fit rounded-2xl bg-white p-3 shadow-pop"><x-ui.logo class="h-9 text-neutral-900" /></div>
            <div class="relative">
                <p class="text-4xl leading-tight font-bold tracking-tight text-balance">{{ __('Print once. Change it any time.') }}</p>
                <ul class="mt-8 space-y-4 text-lg text-white/85">
                    @foreach ([__('Change where a code points after printing.'), __('See scans by day, device and country.'), __('Brand every code with your colours.')] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-1 grid size-6 shrink-0 place-items-center rounded-full bg-white/20"><x-heroicon-m-check class="size-4" /></span>
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <p class="relative text-sm text-white/70">{{ __('Dynamic QR codes you can edit, track and brand.') }}</p>
        </aside>
    </div>

    <x-ui.toast-stack />
    @livewireScripts
</body>
</html>
