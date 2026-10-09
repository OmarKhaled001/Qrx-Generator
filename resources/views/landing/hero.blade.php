{{-- Hero. --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-brand-100/60 via-transparent to-transparent dark:from-brand-950/50" aria-hidden="true"></div>
    <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-2 lg:py-28">
        <div>
            <x-ui.badge color="primary">{{ __('Dynamic QR codes') }}</x-ui.badge>
            <h1 class="mt-5 text-4xl font-semibold tracking-tight text-ink sm:text-5xl lg:text-6xl">{{ __('Print once. Change it any time.') }}</h1>
            <p class="mt-5 max-w-xl text-lg text-ink-muted">{{ __('Create branded QR codes for links, Wi-Fi, contact cards, menus and more. Edit where they point after printing and see how often they are scanned.') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <x-ui.button :href="auth()->check() ? route('qr.create') : route('register')" size="lg" icon="heroicon-o-plus">{{ __('Create a free QR code') }}</x-ui.button>
                <x-ui.button href="#pricing" variant="secondary" size="lg">{{ __('See pricing') }}</x-ui.button>
            </div>
            <p class="mt-4 text-sm text-ink-subtle">{{ __('Start free with 3 dynamic QR codes. No card needed.') }}</p>
        </div>
        <div class="relative mx-auto w-full max-w-sm" aria-hidden="true">
            <div class="absolute -inset-6 rounded-[2rem] bg-linear-to-tr from-brand-400/30 to-accent-400/30 blur-2xl"></div>
            <div class="relative rounded-2xl border border-line bg-surface p-6 shadow-pop">
                <img src="{{ app(\App\Services\QrRenderer::class)->svgDataUri(url('/'), ['style' => 'round', 'eye' => 'circle', 'gradient' => ['from' => '#0e7490', 'to' => '#7c3aed']], 360) }}" alt="" class="aspect-square w-full">
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span class="font-medium text-ink">{{ __('Restaurant menu') }}</span>
                    <x-ui.badge color="success" dot>{{ __('Active') }}</x-ui.badge>
                </div>
            </div>
        </div>
    </div>
</section>
